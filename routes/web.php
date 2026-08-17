<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Web\AssetController;
use App\Http\Controllers\Web\DamageReportController;
use App\Http\Controllers\Web\LaporanPdfController;
use App\Http\Controllers\Web\LaporanSayaController;
use App\Http\Controllers\Web\MaintenanceReportController;
use App\Http\Controllers\Web\RepairReportController;
use App\Http\Controllers\Web\ReportStatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    return redirect()->route('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('/laporan/perawatan', [MaintenanceReportController::class, 'create'])->name('laporan.perawatan');
    Route::post('/laporan/perawatan', [MaintenanceReportController::class, 'store'])->name('laporan.perawatan.store');

    Route::get('/laporan/kerusakan', [DamageReportController::class, 'create'])->name('laporan.kerusakan');
    Route::post('/laporan/kerusakan', [DamageReportController::class, 'store'])->name('laporan.kerusakan.store');
    Route::get('/laporan/kerusakan/{damageReport}/perbaikan', [RepairReportController::class, 'create'])->name('laporan.perbaikan');
    Route::post('/laporan/kerusakan/{damageReport}/perbaikan', [RepairReportController::class, 'store'])->name('laporan.perbaikan.store');

    Route::get('/laporan/status', [ReportStatusController::class, 'show'])->name('laporan.status');
    Route::get('/laporan/saya', [LaporanSayaController::class, 'index'])->name('laporan.saya');
    
    // AJAX endpoints for Laporan Saya
    Route::post('/laporan/saya/reports', [LaporanSayaController::class, 'getReports'])->name('laporan.saya.reports');
    Route::get('/laporan/saya/buildings', [LaporanSayaController::class, 'getBuildings'])->name('laporan.saya.buildings');
    Route::get('/laporan/saya/departments', [LaporanSayaController::class, 'getDepartments'])->name('laporan.saya.departments');
    Route::get('/laporan/saya/rooms', [LaporanSayaController::class, 'getRooms'])->name('laporan.saya.rooms');

    Route::get('/aset', [AssetController::class, 'index'])->name('aset.index');
    Route::get('/aset/{asset}', [AssetController::class, 'detail'])->name('aset.detail');

    Route::get('/laporan/pdf/kerusakan/{damageReport}', [LaporanPdfController::class, 'damage'])->name('laporan.pdf.kerusakan');
    Route::get('/laporan/pdf/kerusakan/{damageReport}/file', [LaporanPdfController::class, 'damageFile'])->name('laporan.pdf.kerusakan.file');
    Route::get('/laporan/pdf/perawatan/{maintenanceReport}', [LaporanPdfController::class, 'maintenance'])->name('laporan.pdf.perawatan');
    Route::get('/laporan/pdf/perawatan/{maintenanceReport}/file', [LaporanPdfController::class, 'maintenanceFile'])->name('laporan.pdf.perawatan.file');
    Route::get('/laporan/pdf/perbaikan/{repairReport}', [LaporanPdfController::class, 'repair'])->name('laporan.pdf.perbaikan');
    Route::get('/laporan/pdf/perbaikan/{repairReport}/file', [LaporanPdfController::class, 'repairFile'])->name('laporan.pdf.perbaikan.file');
});
