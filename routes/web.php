<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataTrainingController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LabellingController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LabeledController;
use App\Http\Controllers\HourlyController;
use App\Http\Controllers\HasilKlasifikasiController;
use App\Http\Controllers\CetakLaporanController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Import routes
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/store', [ImportController::class, 'store'])->name('import.store');
    Route::get('/import/{id}/edit', [ImportController::class, 'edit'])->name('import.edit');
    Route::put('/import/{id}', [ImportController::class, 'update'])->name('import.update');
    Route::delete('/import/{id}', [ImportController::class, 'destroy'])->name('import.destroy');
    Route::post('/import/autolabel', [ImportController::class, 'autolabel'])->name('import.autolabel'); 

    // // Label routes
    // Route::get('/label', [LabelController::class, 'index'])->name('label.index');
    // Route::post('/label/{id}', [LabelController::class, 'store'])->name('label.store');
    // Route::post('/label/autolabel', [LabelController::class, 'autolabel'])->name('label.autolabel');
    // Route::patch('/label/{id}/delete', [LabelController::class, 'delete'])->name('label.delete');

    
    // Labelling routes
    Route::get('/labeled', [LabeledController::class, 'index'])->name('labeled.index');
    Route::post('/labeled/autolabel', [LabeledController::class, 'autolabel'])->name('labeled.autolabel');
    Route::post('/labeled/latihknn', [LabeledController::class, 'latihknn'])->name('labeled.latihknn');
    Route::post('/labeled/{id}', [LabeledController::class, 'store'])->name('labeled.store');
    Route::patch('/labeled/{id}/delete', [LabeledController::class, 'delete'])->name('labeled.delete');
  
});

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/data-training/input', [DataTrainingController::class, 'create'])->name('data-training.input');
Route::post('/data-training/store', [DataTrainingController::class, 'store'])->name('data-training.store');

Route::get('/data-minyak', [HourlyController::class, 'index'])->name('hourly.index');
Route::post('/data-minyak/store', [HourlyController::class, 'store'])->name('hourly.store');

Route::get('/hasil-klasifikasi', [HasilKlasifikasiController::class, 'index'])->name('hasil.klasifikasi');
Route::get('/cetak-laporan', [CetakLaporanController::class, 'index'])->name('cetak.index');
// Route::get('/cetak-laporan/print', [CetakLaporanController::class, 'print'])->name('cetak.print');
Route::get('/laporan/export', [CetakLaporanController::class, 'exportLaporanCPO'])->name('export.laporanCPO');

// web.php
Route::get('cetak/preview', [CetakLaporanController::class, 'preview'])->name('export.preview');
