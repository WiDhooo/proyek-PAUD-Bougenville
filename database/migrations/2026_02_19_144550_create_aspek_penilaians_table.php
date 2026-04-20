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
        Schema::create('aspek_penilaians', function (Blueprint $table) {
            $table->id();
            $table->enum('lingkup', ['Agama & Moral', 'Fisik-Motorik', 'Kognitif', 'Bahasa', 'Sosial-Emosional', 'Seni']);
            $table->string('sub_lingkup'); // Misal: Motorik Kasar, Motorik Halus
            $table->text('indikator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspek_penilaians');
    }
};
