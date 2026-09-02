<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Guru\GuruController;

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
    // Route Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    // Route Dashboard Guru
    Route::get('/guru/dashboard', [GuruController::class, 'index'])->name('guru.dashboard');
});
