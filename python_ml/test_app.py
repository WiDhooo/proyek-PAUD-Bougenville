"""
test_app.py — Tests untuk Python ML Service (Flask).
Jalankan dengan: pytest python_ml/test_app.py -v

Cakupan:
- validate_payload()    → unit tests untuk validasi input
- /health               → endpoint health check
- /analyze              → happy path, 422, 401 (jika API key aktif)
"""

import os
import json
import pytest

# Pastikan ML_API_KEY tidak di-set saat testing (skip auth)
os.environ.pop('ML_API_KEY', None)

# Import setelah env di-set
from app import app, validate_payload


# ============================================================
# Fixtures
# ============================================================

@pytest.fixture
def client():
    """Flask test client."""
    app.config['TESTING'] = True
    with app.test_client() as c:
        yield c


def _make_valid_payload(n=3):
    """Buat payload valid dengan n siswa."""
    return {
        "data": [
            {
                "siswa_id": i,
                "nilai": {
                    "Agama & Moral": 3.5,
                    "Fisik-Motorik": 2.5,
                    "Kognitif": 4.0,
                    "Bahasa": 3.0,
                    "Sosial-Emosional": 2.0,
                    "Seni": 3.5,
                }
            }
            for i in range(1, n + 1)
        ]
    }


# ============================================================
# Unit Tests: validate_payload()
# ============================================================

class TestValidatePayload:

    def test_valid_payload_returns_none(self):
        """Payload valid harus mengembalikan None (tidak ada error)."""
        data = _make_valid_payload()['data']
        assert validate_payload(data) is None

    def test_empty_list_returns_error(self):
        """List kosong harus terdeteksi."""
        assert validate_payload([]) is not None

    def test_none_input_returns_error(self):
        """None input harus terdeteksi."""
        assert validate_payload(None) is not None

    def test_non_list_input_returns_error(self):
        """Input bukan list harus terdeteksi."""
        assert validate_payload({"key": "val"}) is not None

    def test_single_siswa_returns_error(self):
        """Kurang dari 2 siswa harus error (clustering butuh minimal 2)."""
        data = _make_valid_payload(1)['data']
        error = validate_payload(data)
        assert error is not None
        assert "2" in error  # Pesan menyebut minimal 2

    def test_missing_siswa_id_key_returns_error(self):
        """Item tanpa key 'siswa_id' harus terdeteksi."""
        data = [
            {"nilai": {"Kognitif": 3.0, "Bahasa": 2.0}},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0, "Bahasa": 2.0}},
        ]
        assert validate_payload(data) is not None

    def test_missing_nilai_key_returns_error(self):
        """Item tanpa key 'nilai' harus terdeteksi."""
        data = [
            {"siswa_id": 1},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0}},
        ]
        assert validate_payload(data) is not None

    def test_nilai_not_dict_returns_error(self):
        """'nilai' bukan dict harus terdeteksi."""
        data = [
            {"siswa_id": 1, "nilai": [3.0, 2.0]},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0}},
        ]
        assert validate_payload(data) is not None

    def test_nilai_empty_dict_returns_error(self):
        """'nilai' dict kosong harus terdeteksi."""
        data = [
            {"siswa_id": 1, "nilai": {}},
            {"siswa_id": 2, "nilai": {}},
        ]
        assert validate_payload(data) is not None

    def test_null_nilai_returns_error(self):
        """Nilai null dalam aspek harus terdeteksi."""
        data = [
            {"siswa_id": 1, "nilai": {"Kognitif": None, "Bahasa": 2.0}},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0, "Bahasa": 2.0}},
        ]
        assert validate_payload(data) is not None

    def test_nilai_below_range_returns_error(self):
        """Nilai kurang dari 1.0 harus terdeteksi."""
        data = [
            {"siswa_id": 1, "nilai": {"Kognitif": 0.5}},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0}},
        ]
        assert validate_payload(data) is not None

    def test_nilai_above_range_returns_error(self):
        """Nilai lebih dari 4.0 harus terdeteksi."""
        data = [
            {"siswa_id": 1, "nilai": {"Kognitif": 5.0}},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0}},
        ]
        assert validate_payload(data) is not None

    def test_inconsistent_aspek_keys_returns_error(self):
        """Siswa dengan aspek berbeda dari siswa lain harus terdeteksi."""
        data = [
            {"siswa_id": 1, "nilai": {"Kognitif": 3.0, "Bahasa": 2.0}},
            {"siswa_id": 2, "nilai": {"Kognitif": 3.0, "Seni": 3.5}},  # Berbeda!
        ]
        assert validate_payload(data) is not None

    def test_nilai_exactly_1_is_valid(self):
        """Nilai tepat 1.0 (batas bawah) harus diterima."""
        data = [
            {"siswa_id": 1, "nilai": {"Kognitif": 1.0}},
            {"siswa_id": 2, "nilai": {"Kognitif": 1.0}},
        ]
        assert validate_payload(data) is None

    def test_nilai_exactly_4_is_valid(self):
        """Nilai tepat 4.0 (batas atas) harus diterima."""
        data = [
            {"siswa_id": 1, "nilai": {"Kognitif": 4.0}},
            {"siswa_id": 2, "nilai": {"Kognitif": 4.0}},
        ]
        assert validate_payload(data) is None


# ============================================================
# Integration Tests: /health endpoint
# ============================================================

class TestHealthEndpoint:

    def test_health_returns_200(self, client):
        resp = client.get('/health')
        assert resp.status_code == 200

    def test_health_returns_ok_status(self, client):
        resp = client.get('/health')
        body = json.loads(resp.data)
        assert body['status'] == 'ok'

    def test_health_no_api_key_required(self, client):
        """Health check tidak boleh butuh API key."""
        resp = client.get('/health', headers={})
        assert resp.status_code == 200


# ============================================================
# Integration Tests: /analyze endpoint
# ============================================================

class TestAnalyzeEndpoint:

    def test_analyze_empty_body_returns_400(self, client):
        """Body kosong harus 400."""
        resp = client.post('/analyze', content_type='application/json', data='{}')
        assert resp.status_code == 400

    def test_analyze_no_data_key_returns_400(self, client):
        """JSON tanpa key 'data' harus 400."""
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps({"wrong_key": []})
        )
        assert resp.status_code == 400

    def test_analyze_less_than_2_siswa_returns_422(self, client):
        """Kurang dari 2 siswa harus 422."""
        payload = _make_valid_payload(1)
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps(payload)
        )
        assert resp.status_code == 422
        body = json.loads(resp.data)
        assert body['status'] == 'error'

    def test_analyze_null_nilai_returns_422(self, client):
        """Nilai null dalam aspek harus 422."""
        payload = {
            "data": [
                {"siswa_id": 1, "nilai": {"Kognitif": None}},
                {"siswa_id": 2, "nilai": {"Kognitif": 3.0}},
            ]
        }
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps(payload)
        )
        assert resp.status_code == 422

    def test_analyze_valid_payload_returns_200(self, client):
        """Payload valid dengan 3 siswa harus 200 dan success status."""
        payload = _make_valid_payload(3)
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps(payload)
        )
        assert resp.status_code == 200
        body = json.loads(resp.data)
        assert body['status'] == 'success'

    def test_analyze_response_has_clusters(self, client):
        """Response harus memiliki key 'clusters'."""
        payload = _make_valid_payload(4)
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps(payload)
        )
        body = json.loads(resp.data)
        assert 'clusters' in body

    def test_analyze_response_has_cluster_profiles(self, client):
        """Response harus memiliki key 'profiles'."""
        payload = _make_valid_payload(4)
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps(payload)
        )
        body = json.loads(resp.data)
        assert 'profiles' in body


# ============================================================
# Integration Tests: API Key Authentication
# ============================================================

class TestApiKeyAuth:

    def test_no_api_key_env_allows_access(self, client):
        """Jika ML_API_KEY tidak di-set, semua request diizinkan (dev mode)."""
        resp = client.post(
            '/analyze',
            content_type='application/json',
            data=json.dumps({"wrong": "payload"})
        )
        # Tidak boleh 401 — harus 400 (validation error, bukan auth error)
        assert resp.status_code != 401

    def test_wrong_api_key_returns_401(self, client):
        """API key salah harus mengembalikan 401."""
        import app as app_module
        original_key = app_module.API_KEY
        app_module.API_KEY = 'secret-test-key'  # Aktifkan auth sementara

        try:
            resp = client.post(
                '/analyze',
                content_type='application/json',
                data=json.dumps(_make_valid_payload(3)),
                headers={'X-API-Key': 'wrong-key'}
            )
            assert resp.status_code == 401
            body = json.loads(resp.data)
            assert body['status'] == 'error'
        finally:
            app_module.API_KEY = original_key  # Kembalikan ke semula

    def test_correct_api_key_allows_access(self, client):
        """API key benar harus diizinkan masuk."""
        import app as app_module
        original_key = app_module.API_KEY
        app_module.API_KEY = 'secret-test-key'

        try:
            resp = client.post(
                '/analyze',
                content_type='application/json',
                data=json.dumps(_make_valid_payload(3)),
                headers={'X-API-Key': 'secret-test-key'}
            )
            assert resp.status_code == 200
        finally:
            app_module.API_KEY = original_key
