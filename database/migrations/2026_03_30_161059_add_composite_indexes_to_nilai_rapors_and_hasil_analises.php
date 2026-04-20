<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [P-2][P-3] Tambah composite index untuk query lookup yang sering digunakan.
 * Mengoptimasi WHERE siswa_id = ? AND periode = ? AND tahun_ajaran = ?
 */
return new class extends Migration
{
    public function up(): void
    {
        // [P-2] Index untuk tabel nilai_rapors
        Schema::table('nilai_rapors', function (Blueprint $table) {
            $table->index(
                ['siswa_id', 'periode', 'tahun_ajaran'],
                'nilai_rapors_lookup_idx'
            );
        });

        // [P-3] Index untuk tabel hasil_analises
        Schema::table('hasil_analises', function (Blueprint $table) {
            $table->index(
                ['siswa_id', 'periode', 'tahun_ajaran'],
                'hasil_analises_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('nilai_rapors', function (Blueprint $table) {
            $table->dropIndex('nilai_rapors_lookup_idx');
        });

        Schema::table('hasil_analises', function (Blueprint $table) {
            $table->dropIndex('hasil_analises_lookup_idx');
        });
    }
};
