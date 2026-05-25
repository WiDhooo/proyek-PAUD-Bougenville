"""
ml_service.py — Machine Learning Logic ONLY

Agglomerative Clustering with Manhattan distance + Complete linkage.
Dynamic K-selection via Silhouette Score (k_min=3 based on PAUD theory).
Auto-Profiling: calculates cluster centroids for dynamic labeling.

v2.1 — Improvements:
- Deterministic tie-breaking untuk aspek_dominan (variance-aware, fully deterministic)
- Cohesion score per cluster (seberapa kompak cluster)
- Richer profile output untuk SmartAnalysisService
"""

import numpy as np
from sklearn.cluster import AgglomerativeClustering
from sklearn.metrics import silhouette_score


def find_optimal_k(features: np.ndarray, k_min: int = 3, k_max: int = 6) -> tuple:
    """
    Mencari jumlah cluster (K) optimal menggunakan Silhouette Score.

    K_MIN = 3 karena berdasarkan teori PAUD (Permendikbud 137 & Kecerdasan Majemuk Gardner),
    profil anak PAUD minimal terbagi 3-4 tipe perkembangan, tidak mungkin hanya 2.

    Selalu mengembalikan tuple (best_k, best_score).
    """
    # Pastikan k_max tidak melebihi jumlah sampel - 1
    k_max = min(k_max, len(features) - 1)

    if k_max < k_min:
        return (k_min, 0.0)

    best_k = k_min
    best_score = -1

    for k in range(k_min, k_max + 1):
        model = AgglomerativeClustering(
            n_clusters=k,
            metric='manhattan',
            linkage='complete'  # Complete linkage → cluster lebih padat dan spesifik
        )
        labels = model.fit_predict(features)

        # Silhouette score memerlukan minimal 2 cluster unik
        if len(set(labels)) < 2:
            continue

        score = silhouette_score(features, labels, metric='manhattan')

        if score > best_score:
            best_score = score
            best_k = k

    return (best_k, best_score)


def profile_clusters(features: np.ndarray, labels: np.ndarray, aspect_names: list) -> dict:
    """
    AUTO-PROFILING: Menghitung rata-rata nilai per aspek untuk setiap cluster.

    v2.1 — Deterministic tie-breaking untuk aspek_dominan:
    - Priority 1: rata-rata tertinggi (rounded ke 1 desimal untuk toleransi floating point)
    - Priority 2: variance terendah (aspek yang paling KONSISTEN tinggi di semua anggota cluster)
    - Priority 3: alphabetical order (jaminan deterministik absolut)

    Contoh: jika Agama & Moral dan Sosial-Emosional sama-sama avg=3.5,
    maka aspek dengan anggota yang lebih konsisten (variance lebih kecil) yang menang.
    Ini memastikan label cluster tidak berubah-ubah antar run.

    Returns:
        {
            "0": {
                "rata_rata_aspek": {"Agama & Moral": 3.5, "Fisik-Motorik": 1.8, ...},
                "aspek_dominan": "Agama & Moral",      ← SELALU KONSISTEN
                "aspek_terendah": "Fisik-Motorik",
                "cohesion_score": 0.82,                ← BARU: 0.0-1.0, semakin tinggi semakin kompak
                "jumlah_siswa": 8
            },
            ...
        }
    """
    unique_clusters = sorted(set(labels))
    profiles = {}

    for cluster_id in unique_clusters:
        # Ambil semua data siswa yang masuk ke cluster ini
        cluster_data = features[labels == cluster_id]

        # Hitung rata-rata dan variance setiap aspek di cluster ini
        avg_scores = np.mean(cluster_data, axis=0)
        var_scores = np.var(cluster_data, axis=0)

        # Buat dict aspek → rata-rata & variance
        aspek_dict = {}
        aspek_var_dict = {}
        for i, name in enumerate(aspect_names):
            aspek_dict[name] = round(float(avg_scores[i]), 2)
            aspek_var_dict[name] = float(var_scores[i])

        # ==============================================================
        # FIX: DETERMINISTIC TIE-BREAKING untuk aspek_dominan
        # ==============================================================
        # Python's max() tidak deterministik jika dua nilai sama.
        # Solusi: gunakan tuple sort key multi-level:
        # (avg_score, -variance, -alphabetical_score)
        #
        # Contoh tie-breaking:
        # - Agama & Moral: avg=3.5, var=0.05  → key=(3.5, -0.05, ...)
        # - Sosial-Emosional: avg=3.5, var=0.25 → key=(3.5, -0.25, ...)
        # → Agama & Moral MENANG karena -0.05 > -0.25
        # ==============================================================
        aspek_dominan = max(
            aspek_dict,
            key=lambda k: (
                round(aspek_dict[k], 1),    # Primary: skor rata-rata (rounded ke 1 desimal)
                -aspek_var_dict[k],          # Secondary: variance TERKECIL (konsisten)
                k                            # Tertiary: alphabetical (absolut deterministik)
            )
        )

        # Deterministic tie-breaking untuk aspek_terendah juga:
        # Priority 1: rata-rata terendah
        # Priority 2: variance TERBESAR (paling tidak konsisten = paling perlu perhatian)
        # Priority 3: alphabetical
        aspek_terendah = min(
            aspek_dict,
            key=lambda k: (
                round(aspek_dict[k], 1),    # Primary: skor rata-rata (rounded ke 1 desimal)
                -aspek_var_dict[k],          # Secondary: variance TERBESAR (perlu lebih diperhatikan)
                k                            # Tertiary: alphabetical
            )
        )

        # ==============================================================
        # Cohesion Score: seberapa kompak/homogen cluster ini
        # Score 1.0 = semua anggota cluster sangat mirip satu sama lain
        # Score 0.0 = anggota cluster sangat tersebar
        # Formula: 1 - (mean_variance / MAX_POSSIBLE_VAR)
        # Untuk skala 1-4, variance max theoretically = 2.25 (jika setengah nilai 1 dan setengah 4)
        # ==============================================================
        mean_variance = float(np.mean(var_scores))
        MAX_POSSIBLE_VAR = 2.25  # ((4-1)/2)^2 = 2.25 untuk skala 1-4
        cohesion_score = round(max(0.0, 1.0 - (mean_variance / MAX_POSSIBLE_VAR)), 3)

        profiles[str(cluster_id)] = {
            "rata_rata_aspek": aspek_dict,
            "aspek_dominan": aspek_dominan,
            "aspek_terendah": aspek_terendah,
            "cohesion_score": cohesion_score,  # Kompakitas cluster (0.0-1.0)
            "jumlah_siswa": len(cluster_data)
        }

    return profiles


def run_clustering(data: list) -> dict:
    """
    Menjalankan Agglomerative Clustering pada data nilai siswa.

    Args:
        data: List of dict, setiap dict berisi:
              - 'siswa_id': int
              - 'nilai': dict {aspek_name: rata_rata_score}
                  Contoh: {"Agama & Moral": 3.5, "Fisik-Motorik": 2.0, ...}
              Data sudah TERVALIDASI dan DIAGREGASI oleh Laravel.

    Returns:
        dict berisi: optimal_k, silhouette_score, clusters, profiles
    """
    siswa_ids = [item['siswa_id'] for item in data]

    # Ambil daftar nama aspek dari siswa pertama (urutan konsisten)
    aspect_names = list(data[0]['nilai'].keys())

    # Konversi dict nilai ke numpy array (urutan mengikuti aspect_names)
    features = np.array([
        [item['nilai'][aspect] for aspect in aspect_names]
        for item in data
    ], dtype=float)

    # Cari K optimal
    optimal_k, sil_score = find_optimal_k(features)

    # Jika sampel kurang dari k_min, gunakan k = jumlah sampel - 1
    if optimal_k > len(features) - 1:
        optimal_k = max(2, len(features) - 1)

    # Jalankan clustering final dengan K optimal
    model = AgglomerativeClustering(
        n_clusters=optimal_k,
        metric='manhattan',
        linkage='complete'
    )
    labels = model.fit_predict(features)

    # Map hasil ke siswa_id
    clusters = {}
    for i, siswa_id in enumerate(siswa_ids):
        clusters[str(siswa_id)] = int(labels[i])

    # AUTO-PROFILING: Analisis karakteristik setiap cluster (v2.1 — deterministic)
    profiles = profile_clusters(features, labels, aspect_names)

    return {
        "optimal_k": optimal_k,
        "silhouette_score": round(float(sil_score), 4),
        "clusters": clusters,
        "profiles": profiles
    }
