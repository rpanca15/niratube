<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute untuk Videos API
Route::resource('/videos', VideoController::class)->except(['store', 'update', 'destroy']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/videos', [VideoController::class, 'store']); // Tambah video
    Route::put('/videos/{video}', [VideoController::class, 'update']); // Update video
    Route::delete('/videos/{video}', [VideoController::class, 'destroy']); // Hapus video

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});
