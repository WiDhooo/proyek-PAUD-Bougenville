"""
app.py — HTTP Routing & Strict Validation ONLY.
Semua logika ML ada di ml_service.py.
"""

from flask import Flask, request, jsonify
from ml_service import run_clustering
import traceback

app = Flask(__name__)

# ============================================================
# GUARD RAILS: Strict Input Validation
# ============================================================

def validate_payload(data: list) -> str | None:
    """
    Validasi ketat payload dari Laravel.
    Return None jika valid, return pesan error jika tidak valid.

    Format baru (sudah diagregasi oleh Laravel):
    [
        {"siswa_id": 1, "nilai": {"Agama & Moral": 3.33, "Fisik-Motorik": 2.67, ...}},
        {"siswa_id": 2, "nilai": {"Agama & Moral": 2.0, "Fisik-Motorik": 3.5, ...}},
    ]

    Rules:
    - Setiap item harus punya 'siswa_id' dan 'nilai' (dict)
    - Setiap nilai harus numeric 1.0-4.0 (sudah dirata-ratakan)
    - TIDAK BOLEH null/None
    - Semua siswa harus punya aspek yang sama
    """
    if not data or not isinstance(data, list):
        return "Payload 'data' harus berupa array yang tidak kosong."

    if len(data) < 2:
        return "Minimal 2 siswa diperlukan untuk analisis clustering."

    expected_keys = None

    for item in data:
        if 'siswa_id' not in item or 'nilai' not in item:
            return "Setiap item harus memiliki key 'siswa_id' dan 'nilai'."

        siswa_id = item['siswa_id']
        nilai = item['nilai']

        if not isinstance(nilai, dict) or len(nilai) == 0:
            return f"siswa_id={siswa_id}: 'nilai' harus berupa object/dict yang tidak kosong."

        # Cek konsistensi aspek
        if expected_keys is None:
            expected_keys = set(nilai.keys())
        elif set(nilai.keys()) != expected_keys:
            return (
                f"siswa_id={siswa_id}: aspek penilaian tidak konsisten "
                f"dengan siswa lain. Expected: {expected_keys}"
            )

        # Cek setiap nilai
        for aspek, val in nilai.items():
            if val is None:
                return (
                    f"Nilai tidak valid pada siswa_id={siswa_id}, "
                    f"aspek '{aspek}': ditemukan null."
                )
            if not isinstance(val, (int, float)) or val < 1.0 or val > 4.0:
                return (
                    f"Nilai tidak valid pada siswa_id={siswa_id}, "
                    f"aspek '{aspek}': ditemukan '{val}'. "
                    f"Nilai harus antara 1.0 - 4.0."
                )

    return None  # Valid


# ============================================================
# ROUTES
# ============================================================

@app.route('/analyze', methods=['POST'])
def analyze():
    """Endpoint utama: menerima data nilai teragregasi, mengembalikan hasil clustering + profiles."""
    try:
        payload = request.get_json(silent=True)

        if not payload or 'data' not in payload:
            return jsonify({
                "status": "error",
                "message": "Request body harus berupa JSON dengan key 'data'."
            }), 400

        data = payload['data']

        # GUARD RAILS: Validasi ketat
        validation_error = validate_payload(data)
        if validation_error:
            return jsonify({
                "status": "error",
                "message": validation_error
            }), 422

        # Proses clustering (delegasikan ke ml_service.py)
        result = run_clustering(data)

        return jsonify({
            "status": "success",
            **result
        }), 200

    except Exception as e:
        traceback.print_exc()
        return jsonify({
            "status": "error",
            "message": f"Internal server error: {str(e)}"
        }), 500


@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint."""
    return jsonify({"status": "ok", "service": "PAUD ML Rapor Digital"}), 200


if __name__ == '__main__':
    import os
    debug_mode = os.environ.get('FLASK_DEBUG', 'false').lower() == 'true'
    app.run(host='0.0.0.0', port=5001, debug=debug_mode)
