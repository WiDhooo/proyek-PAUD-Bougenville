"""
ml_service.py — Machine Learning Logic ONLY
Agglomerative Clustering with Manhattan distance + Complete linkage.
Dynamic K-selection via Silhouette Score (k_min=3 based on PAUD theory).
Auto-Profiling: calculates cluster centroids for dynamic labeling.
"""

import numpy as np
from sklearn.cluster import AgglomerativeClustering
from sklearn.metrics import silhouette_score


def find_optimal_k(features: np.ndarray, k_min: int = 3, k_max: int = 6) -> tuple:
    """
    Mencari jumlah cluster (K) optimal menggunakan Silhouette Score.
    K_MIN = 3 karena berdasarkan teori PAUD (Permendikbud 137 & Kecerdasan Majemuk),
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
    Ini membantu Laravel menentukan 'gaya belajar' untuk cluster tersebut
    secara dinamis (bukan hardcoded).

    Returns:
        {
            "0": {
                "rata_rata_aspek": {"Agama & Moral": 3.5, "Fisik-Motorik": 1.8, ...},
                "aspek_dominan": "Agama & Moral",
                "aspek_terendah": "Fisik-Motorik",
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

        # Hitung rata-rata setiap aspek di cluster ini
        avg_scores = np.mean(cluster_data, axis=0)

        # Buat dict aspek → rata-rata
        aspek_dict = {}
        for i, name in enumerate(aspect_names):
            aspek_dict[name] = round(float(avg_scores[i]), 2)

        # Tentukan aspek dominan (tertinggi) dan terendah
        aspek_dominan = max(aspek_dict, key=aspek_dict.get)
        aspek_terendah = min(aspek_dict, key=aspek_dict.get)

        profiles[str(cluster_id)] = {
            "rata_rata_aspek": aspek_dict,
            "aspek_dominan": aspek_dominan,
            "aspek_terendah": aspek_terendah,
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

    # AUTO-PROFILING: Analisis karakteristik setiap cluster
    profiles = profile_clusters(features, labels, aspect_names)

    return {
        "optimal_k": optimal_k,
        "silhouette_score": round(float(sil_score), 4),
        "clusters": clusters,
        "profiles": profiles
    }
