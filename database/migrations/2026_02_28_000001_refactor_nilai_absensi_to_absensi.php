<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop tabel lama yang tidak dipakai
        Schema::dropIfExists('nilai_absensi');

        // 2. Buat tabel baru yang proper
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['H', 'S', 'I', 'A'])->default('H');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            // 1 siswa hanya bisa punya 1 record absensi per jadwal per tanggal
            $table->unique(['jadwal_id', 'siswa_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');

        // Restore tabel lama jika rollback
        Schema::create('nilai_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('absensi', ['H', 'S', 'I', 'A'])->nullable();
            $table->integer('nilai')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }
};
