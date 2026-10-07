<div align="center">

  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" alt="Laravel Logo" width="350">

  # 🏫 MTS-Absen
  ### Aplikasi Absensi Sekolah Berbasis Laravel

  Sistem manajemen absensi digital yang responsif, aman, dan efisien untuk mendukung operasional Madrasah Tsanawiyah (MTS).

  [![PHP Version](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![Laravel Version](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![MySQL](https://img.shields.io/badge/MySQL-Supported-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
  [![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

---

## 📌 Tentang Proyek

**MTS-Absen** hadir untuk memodernisasi pencatatan kehadiran siswa dan staf pengajar. Dirancang dengan antarmuka yang ramah pengguna serta fleksibilitas tinggi, aplikasi ini mempermudah proses pemantauan absensi secara real-time.

### ✨ Fitur Utama

- 👨‍🏫 **Absensi Guru & Staf**: Pencatatan jam masuk dan pulang secara akurat.
- 👨‍🎓 **Absensi Siswa**: Pengelolaan presensi harian per kelas.
- 📊 **Laporan & Rekapitulasi**: Ekspor rekap kehadiran harian, mingguan, dan bulanan.
- 🔐 **Manajemen Akses**: Sistem autentikasi dan otorisasi role berbasis keamanan tinggi.
- 📱 **Desain Responsif**: Otomatis menyesuaikan tampilan di layar HP, tablet, maupun PC.

---

## ⚙️ Persyaratan Sistem

Pastikan lingkungan server lokal Anda memenuhi spesifikasi minimum berikut:

| Komponen | Versi Minimum / Catatan |
| :--- | :--- |
| **PHP** | `^8.3` |
| **Framework** | Laravel `13.x` |
| **Database** | MySQL / MariaDB / SQLite |
| **Package Manager** | Composer `2.x` & Node.js (`npm`) |
| **Local Server** | Laragon (Rekomendasi) / XAMPP |

---

## 🚀 Panduan Instalasi & Setup

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di lingkungan lokal:

### 1. Clone Repository
<pre><code>git clone https://github.com/AsriBeng/mts-absen.git
cd mts-absen</code></pre>

### 2. Instalasi Dependensi
<pre><code># Install paket backend PHP
composer install

# Install paket frontend Node.js
npm install</code></pre>

### 3. Konfigurasi Environment
<pre><code># Duplikasi file konfig .env
cp .env.example .env

# Generate application key
php artisan key:generate</code></pre>

### 4. Setup Database & Migrasi
Sesuaikan kredensial database pada file `.env`, lalu jalankan migrasi beserta data awal:
<pre><code>php artisan migrate --seed</code></pre>

### 5. Build Assets & Jalankan Server
<pre><code># Build stylesheet & script
npm run build

# Jalankan server lokal
php artisan serve</code></pre>

Aplikasi sekarang dapat diakses melalui browser di **`http://127.0.0.1:8000`**.

> 💡 **Pengguna Laragon:** Anda cukup menempatkan folder proyek di `C:\laragon\www\mts-absen` dan mengontrol virtual host bawaan Laragon (`http://mts-absen.test`).

---

## 🛠️ Perintah Pengembangan (Development)

- **Menjalankan Dev Server Frontend (Hot Reload):**
  <pre><code>npm run dev</code></pre>
- **Menjalankan Automated Testing:**
  <pre><code>php artisan test</code></pre>
- **Merapikan Format Kode (Formatting):**
  <pre><code>php artisan pint</code></pre>

---

## 👨‍💻 Pengembang

<div align="center">

  **AsriBeng**  
  *Software Developer & IT Specialist*

  [![GitHub](https://img.shields.io/badge/GitHub-AsriBeng-181717?style=flat-square&logo=github)](https://github.com/AsriBeng)
  [![Email](https://img.shields.io/badge/Email-asrisakbar123%40gmail.com-D14836?style=flat-square&logo=gmail&logoColor=white)](mailto:asrisakbar123@gmail.com)

</div>

---

<div align="center">
  <sub>Dibuat dengan ❤️ untuk kemudahan pengelolaan presensi pendidikan.</sub>
</div>
