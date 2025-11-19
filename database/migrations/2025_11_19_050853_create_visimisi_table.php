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
        Schema::create('visimisi', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel 'profil'
            $table->foreignId('profil_id')->constrained('profil')->onDelete('cascade');
            $table->enum('tipe', ['visi', 'misi']); // Tipe: 'visi' atau 'misi'
            $table->text('isi'); // Kolom 'isi' (content) sesuai model VisiMisi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visimisi');
    }
};
