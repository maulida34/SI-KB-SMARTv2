<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kelurahan/{kelurahan}', [HomeController::class, 'village'])->name('public.village');

Route::get('/dashboard', fn () => redirect()->route('upload.index'))
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::post('/upload/preview', [UploadController::class, 'preview'])->name('upload.preview');
    Route::post('/upload/save', [UploadController::class, 'save'])->name('upload.save');
    Route::get('/upload/template', [UploadController::class, 'template'])->name('upload.template');
    Route::delete('/upload/batches/{batch}', [UploadController::class, 'deleteBatch'])->name('upload.batches.delete');
});

require __DIR__.'/auth.php';
