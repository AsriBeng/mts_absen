<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            // Terhubung ke tabel users (jika akun login dibuat terpisah)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Data Pribadi Guru
            $table->string('nip', 30)->unique()->nullable(); // NIP/NUPTK
            $table->string('nik', 16)->unique()->nullable(); // NIK KTP
            $table->string('nama_lengkap');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'])->default('Islam');
            $table->text('alamat')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->enum('status_kepegawaian', ['GTY', 'GTT', 'PNS', 'PPPK'])->nullable();
            $table->string('jabatan')->nullable(); // misal: Guru Mapel, Wali Kelas

            // Foto Profil
            $table->string('foto_profile')->nullable(); // Path lokasi foto di storage

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};
