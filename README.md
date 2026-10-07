<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# MTS-Absen - Aplikasi Absensi Sekolah Berbasis Laravel

**MTS-Absen** adalah aplikasi absensi siswa dan guru untuk sekolah menengah pertama (MTS) yang dibangun menggunakan framework Laravel. Aplikasi ini dirancang untuk memudahkan proses absensi dengan fitur-fitur modern dan responsif.

## Fitur Utama

- **Absensi Siswa**: Catat kehadiran siswa secara real-time dengan barcode scanner.
- **Absensi Guru**: Sistem absensi untuk guru dengan jadwal pelajaran.
- **Laporan Kehadiran**: Generate laporan absensi harian, mingguan, dan bulanan.
- **Manajemen Pengguna**: Sistem login dan otorisasi yang aman.
- **Notifikasi**: Pengingat absensi dan pengumuman.

## Persyaratan Sistem

- **PHP**: ^8.3
- **Laravel**: ^13.17 (Laravel 13 dengan fitur AI dan boost)
- **MySQL** atau **SQLite** (disarankan untuk development)
- **Composer** (untuk manajemen dependensi PHP)
- **Node.js** dan **npm** (untuk asset management dan build)
- **Laragon** atau **XAMPP** (untuk pengembangan lokal di Windows)

## Cara Clone dan Setup Proyek

1. **Clone Repository**
   ```bash
   git clone https://github.com/AsriBeng/mts-absen.git
   cd mts-absen
   ```

2. **Instalasi Dependensi PHP**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Instalasi Node.js Packages**
   ```bash
   npm install
   ```

5. **Generate Assets**
   ```bash
   npm run build
   ```

6. **Setup Database**
   - Untuk development dengan SQLite:
     ```bash
     php artisan migrate --seed
     ```
   - Atau untuk MySQL (set di `.env`):
     ```bash
     php artisan migrate --seed
     ```

7. **Run Server**
   ```bash
   php artisan serve
   ```
   Atau dengan Laragon:
   - Copy proyek ke folder `c:\laragon\www\mts-absen`
   - Akses melalui browser: `http://localhost/mts-absen/public`

## Pengembangan

- **Testing**: `php artisan test`
- **Linting**: `php artisan pint`
- **Watch Development**: `npm run dev`

## Versi yang Digunakan

- **Laravel**: 13.17
- **PHP**: 8.3
- **Composer**: Terbaru (2.x)
- **Node.js**: Sesuai package.json

## Pembuat

**AsriBeng**  
GitHub: [https://github.com/AsriBeng](https://github.com/AsriBeng)  
Email: [asribeng@example.com](mailto:asribeng@example.com) (ganti dengan email asli jika ada)

Terima kasih atas kepercayaannya menggunakan aplikasi ini. Semoga membantu proses absensi sekolah Anda! 🙏

## Kontribusi

Buka issue atau pull request jika ada fitur yang ingin ditambahkan atau bug yang ingin diperbaiki.
