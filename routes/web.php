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

// Redirect awal berdasarkan status login
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if ($user->role && $user->role->name === 'admin') {
        return redirect('/admin/dashboard');
    }

    return redirect('/guru/dashboard');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // === GROUP ADMIN ===
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Absensi Admin
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');
        Route::post('/absensi/generate-key', [AbsensiController::class, 'generateKey'])->name('absensi.generate');

        // Rekap Admin
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap');
        Route::get('/rekap/export-pdf', [RekapController::class, 'exportPdf'])->name('rekap.export_pdf');

        // Users Setting
        Route::get('/users-setting', [UserSettingController::class, 'index'])->name('users_setting');
        Route::post('/users-setting', [UserSettingController::class, 'store'])->name('users_setting.store');
        Route::put('/users-setting/{id}', [UserSettingController::class, 'update'])->name('users_setting.update');
        Route::delete('/users-setting/{id}', [UserSettingController::class, 'destroy'])->name('users_setting.destroy');

        // Days Setting
        Route::get('/days', [DaySettingController::class, 'index'])->name('days');
        Route::post('/days', [DaySettingController::class, 'store'])->name('days.store');
        Route::put('/days/{id}', [DaySettingController::class, 'update'])->name('days.update');
        Route::delete('/days/{id}', [DaySettingController::class, 'destroy'])->name('days.destroy');

        // Profile & Setting Admin
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
        Route::get('/setting', [ProfileController::class, 'showSetting'])->name('setting');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // === GROUP GURU ===
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', [GuruController::class, 'index'])->name('dashboard');
        Route::get('/absen-saya', [AbsenSayaController::class, 'index'])->name('absen_saya');
        Route::post('/absen-saya/scan', [AbsenSayaController::class, 'storeScan'])->name('absen_saya.scan');
        Route::get('/absen-saya/export-pdf', [AbsenSayaController::class, 'exportPdf'])->name('absen_saya.export_pdf');

        // Profile & Setting Guru
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
        Route::get('/setting', [ProfileController::class, 'showSetting'])->name('setting');
        Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

});
