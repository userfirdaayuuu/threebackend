<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\KursusController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UseresController;
use App\Http\Controllers\KursusDetailController;
use App\Http\Controllers\CloudinaryController;
use App\Http\Controllers\MaterialUserController;


//akun
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy']);
});
Route::middleware('auth:api')->get('/akun-saya', [UseresController::class, 'akunSaya']);
// routes/api.php
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()
    ]);
});


//kursus
Route::post('/kursus', [KursusController::class, 'store']);
Route::get('/kursus/{id}', [KursusController::class, 'show']);
Route::get('/kursus', [KursusController::class, 'index']);

//kursus detail
Route::post('/kursus-detail', [KursusDetailController::class, 'store']);
Route::get('/kursus-detail/by-kursus/{kursus_id}', [KursusDetailController::class, 'byKursusId']);
Route::put('/kursus-detail/by-kursus/{kursus_id}/edit', [KursusDetailController::class, 'updatebyKursusId']);

Route::middleware('auth:api')->get('/materi/kursus-siswa', [UseresController::class, 'kursusSiswa']);
Route::middleware('auth:api')->get('/materi/kursus-tutor', [UseresController::class, 'kursusTutor']);

//material
Route::get('/materials', [MaterialController::class, 'index']);
Route::delete('/materials/{id}', [MaterialController::class, 'destroy']);
Route::middleware('auth:api')->get('/materials/kursus/{id}', [MaterialController::class, 'getByKursus']);
// Route::middleware('auth:api')->get('/materials/kursus/{id}/{materialId}', [MaterialController::class, 'showDetail']);
Route::get('/materials/kursus/{id}/{materialId}', [MaterialController::class, 'showDetail']);
Route::middleware('auth:api')->post('/materials/upload/{kursus_id}', [MaterialController::class, 'upload']);

//status selesai
Route::middleware('auth:api')->post('/materi/{materialId}/selesai', [MaterialUserController::class, 'tandaiSelesai']);


Route::get('/siswa', [UseresController::class, 'getAllSiswa']);
Route::get('/kursus/{id}/siswa', [KursusController::class, 'getSiswaByKursus']);
Route::get('/kursus/{id}/tutor', [UseresController::class, 'getKursusTutor']);

//dokumen
Route::post('/cloudinary/upload', [CloudinaryController::class, 'uploadDocument']);
Route::post('/cloudinary/delete', [CloudinaryController::class, 'delete']);
Route::get('/documents', [CloudinaryController::class, 'index']);

Route::get('/ping', function (Request $request) {
    return response()->json(['message' => 'pong']);
});

// Route::middleware('auth:api')->post('/siswa/materi/{material_id}/completed', [UseresController::class, 'tandaiMateriSelesai']);
// Route::get('/materi/{material_id}/completed-count', [UseresController::class, 'getCompletedCount']);
// Route::middleware('auth:api')->get('/kursus/{id}/siswa', [UseresController::class, 'getSiswaByKursus']);
// Route::get('/pengguna/{id}/kursus-diikuti', [UseresController::class, 'getKursusDiikuti']);
// Route::get('/kursus-detail/{id}', [KursusDetailController::class, 'showKursusDetail']);
