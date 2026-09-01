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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('absensi_key_id')->nullable()->constrained('absensi_keys')->onDelete('set null');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->string('image_in')->nullable();  // Foto selfie masuk
            $table->string('image_out')->nullable(); // Foto selfie pulang
            $table->decimal('user_latitude', 10, 8)->nullable();
            $table->decimal('user_longitude', 11, 8)->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'izin', 'alpa']);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
