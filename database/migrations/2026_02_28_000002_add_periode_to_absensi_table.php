<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->enum('periode', ['Ganjil', 'Genap'])->default('Ganjil')->after('keterangan');
            $table->string('tahun_ajaran', 9)->default('2026/2027')->after('periode');
        });
    }

    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            $table->dropColumn(['periode', 'tahun_ajaran']);
        });
    }
};
