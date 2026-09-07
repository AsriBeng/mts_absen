<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_settings', function (Blueprint $table) {
            $table->enum('status', ['masuk', 'libur'])->default('masuk')->after('radius_meters');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_settings', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
