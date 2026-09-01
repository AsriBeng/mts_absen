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
        Schema::create('absensi_settings', function (Blueprint $table) {
            $table->id();
            $table->string('day_name'); // Senin, Selasa, dst.
            $table->time('time_in');
            $table->time('time_out');
            $table->integer('late_tolerance_minutes')->default(0);
            $table->decimal('office_latitude', 10, 8);
            $table->decimal('office_longitude', 11, 8);
            $table->integer('radius_meters')->default(50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_settings');
    }
};
