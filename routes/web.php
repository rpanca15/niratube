<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes yang memerlukan login
Route::middleware(['auth'])->group(function () {
    Route::resource('/videos', VideoController::class)->except(['show']); // Semua kecuali 'show'
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Routes untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Routes tanpa autentikasi
Route::get('/videos/{id}', [VideoController::class, 'show'])->name('videos.show');
Route::post('/videos/{id}/like', [VideoController::class, 'likeVideo'])->name('videos.like');
Route::post('/videos/{id}/increment-views', [VideoController::class, 'incrementViews'])->name('videos.incrementViews');
