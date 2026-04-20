<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_rekomendasis', function (Blueprint $table) {
            $table->id();
            $table->string('label_cluster')->unique(); // Misal: Cluster 0, Cluster 1
            $table->string('nama_kelompok')->nullable(); // Misal: Kelompok Eksploratif
            $table->text('deskripsi_gaya_belajar');
            $table->text('saran_kegiatan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_rekomendasis');
    }
};
