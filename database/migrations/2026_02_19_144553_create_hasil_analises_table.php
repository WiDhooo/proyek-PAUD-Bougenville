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
        Schema::create('hasil_analises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('cluster_group'); // Menyimpan ID cluster dari Python
            $table->string('periode')->nullable();
            $table->string('tahun_ajaran')->nullable();
            $table->json('raw_response')->nullable(); // Opsional: simpan raw response dari Python jika perlu debug
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_analises');
    }
};
