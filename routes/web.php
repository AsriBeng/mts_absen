<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\UserSettingController;
use App\Http\Controllers\Admin\DaySettingController;

use App\Http\Controllers\Guru\GuruController;
use App\Http\Controllers\Guru\AbsenSayaController;

use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    // 1. Jika pengguna belum login, arahkan ke halaman login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // 2. Jika sudah login, arahkan sesuai role masing-masing
    if ($user->role && $user->role->name === 'admin') {
        return redirect('/admin/dashboard');
    }

    return redirect('/guru/dashboard');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    // Route Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/absensi', [AbsensiController::class, 'index'])->name('admin.absensi');
    Route::post('/admin/absensi/generate-key', [AbsensiController::class, 'generateKey'])->name('admin.absensi.generate');
    Route::get('/admin/rekap', [RekapController::class, 'index'])->name('admin.rekap');
    Route::get('/admin/rekap/export-pdf', [RekapController::class, 'exportPdf'])->name('admin.rekap.export_pdf');

    Route::get('/admin/users-setting', [UserSettingController::class, 'index'])->name('admin.users_setting');
    Route::post('/admin/users-setting', [UserSettingController::class, 'store'])->name('admin.users_setting.store');
    Route::put('/admin/users-setting/{id}', [UserSettingController::class, 'update'])->name('admin.users_setting.update');
    Route::delete('/admin/users-setting/{id}', [UserSettingController::class, 'destroy'])->name('admin.users_setting.destroy');

    Route::get('/admin/days', [DaySettingController::class, 'index'])->name('admin.days');
    Route::post('/admin/days', [DaySettingController::class, 'store'])->name('admin.days.store');
    Route::put('/admin/days/{id}', [DaySettingController::class, 'update'])->name('admin.days.update');
    Route::delete('/admin/days/{id}', [DaySettingController::class, 'destroy'])->name('admin.days.destroy');

    // Route Guru
    Route::get('/guru/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');
    Route::get('/guru/absen-saya', [AbsenSayaController::class, 'index'])->name('guru.absen_saya');
    Route::post('/guru/absen-saya/scan', [AbsenSayaController::class, 'storeScan'])->name('guru.absen_saya.scan');
    Route::get('/guru/absen-saya/export-pdf', [AbsenSayaController::class, 'exportPdf'])->name('guru.absen_saya.export_pdf');

    // Rute Profil & Setting Sisi Admin
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
        Route::get('/setting', [ProfileController::class, 'showSetting'])->name('setting');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // Rute Profil & Setting Sisi Guru
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
        Route::get('/setting', [ProfileController::class, 'showSetting'])->name('setting');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
});
