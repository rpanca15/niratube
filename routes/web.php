<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

// Halaman utama
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rute untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

// Rute untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::resource('/videos', VideoController::class)->except(['show']);
    Route::post('/videos/add-to-playlist', [VideoController::class, 'addToPlaylist'])->name('videos.addToPlaylist');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Rute yang bisa diakses tanpa autentikasi
Route::get('/videos/{id}', [VideoController::class, 'show'])->name('videos.show');

// Rute untuk aksi spesifik terkait video
Route::post('/videos/{id}/like', [VideoController::class, 'likeVideo'])
    ->name('videos.like');
Route::post('/videos/{id}/increment-views', [VideoController::class, 'incrementViews'])
    ->name('videos.incrementViews');
