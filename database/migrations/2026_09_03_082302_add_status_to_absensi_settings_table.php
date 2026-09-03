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
        Schema::table('absensi_settings', function (Blueprint $table) {
            // Menambahkan kolom status dengan enum ('masuk', 'libur')
            // Default diset ke 'masuk'
            $table->enum('status', ['masuk', 'libur'])
                  ->default('masuk')
                  ->after('radius_meters'); // Menempatkan kolom setelah radius_meters
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_settings', function (Blueprint $table) {
            // Rollback / hapus kolom jika migration di-rollback
            $table->dropColumn('status');
        });
    }
};
