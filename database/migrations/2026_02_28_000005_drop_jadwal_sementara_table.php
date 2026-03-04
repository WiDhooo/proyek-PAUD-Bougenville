<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('jadwal_sementara');
    }

    public function down(): void
    {
        // Tidak perlu restore — fitur jadwal sementara sudah dihapus
    }
};
