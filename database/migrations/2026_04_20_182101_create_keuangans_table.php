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
        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('kategori', ['Pendaftaran', 'SPP'])->default('SPP');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->string('bulan_pembayaran');
            $table->enum('status', ['Sudah Bayar', 'Belum Bayar'])->default('Sudah Bayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keuangans');
    }
};