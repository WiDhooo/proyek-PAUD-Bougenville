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
        Schema::create('nilai_rapors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('aspek_penilaian_id')->constrained('aspek_penilaians')->onDelete('cascade');
            $table->integer('nilai')->comment('1:BB, 2:MB, 3:BSH, 4:BSB');
            $table->string('periode')->nullable()->comment('Contoh: Ganjil, Genap');
            $table->string('tahun_ajaran')->nullable()->comment('Contoh: 2025/2026');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_rapors');
    }
};
