<?php

use App\Http\Controllers\CMS\KasmasukController;
use App\Http\Controllers\CMS\KegiatanController;
use App\Http\Controllers\CMS\KriteriaController;
use App\Http\Controllers\CMS\MasterController;
use App\Http\Controllers\CMS\SawController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Ui.utama');
});

Route::get('/dashboard', function () {
    return view('Admin.Dashboard');
});

Route::get('/master', function () {
    return view('Admin.master');
});

Route::get('/kas_masuk', function () {
    return view('Admin.kas_masuk');
});
Route::get('/kegiatan', function () {
    return view('Admin.kegiatan');
});

Route::get('/kriteria', function () {
    return view('Admin.kriteria');
});
Route::get('/normalisasi', function () {
    return view('Admin.Normalilasi');
});
Route::get('/keputusan', function () {
    return view('Admin.keputusan');
});
Route::get('/kas-keluar', function () {
    return view('Admin.kas-out');
});


Route::prefix('saw')->group(function () {

    // lokasi kantor
    Route::prefix('master')->controller(MasterController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::post('/create', 'createData');
        Route::get('/get/{id}', 'getDataById');
        Route::post('/update/{id}', 'updateData');
        Route::delete('/delete/{id}', 'deleteData');

        Route::get('/kas-out', 'getKasOut');
    });

    Route::prefix('kas-masuk')->controller(KasmasukController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::post('/create', 'createData');
        Route::get('/get/{id}', 'getDataById');
        Route::post('/update/{id}', 'updateData');
        Route::delete('/delete/{id}', 'deleteData');
        Route::get('/master-kas', 'getAllKas');
    });

    Route::prefix('kegiatan')->controller(KegiatanController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::post('/create', 'createData');
        Route::get('/get/{id}', 'getDataById');
        Route::post('/update/{id}', 'updateData');
        Route::delete('/delete/{id}', 'deleteData');

        Route::post('/update-status/{id}/{status}', 'updateStatus');
    });

    Route::prefix('kriteria')->controller(KriteriaController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::post('/create', 'createData');
        Route::get('/get/{id}', 'getDataById');
        Route::post('/update/{id}', 'updateData');
        Route::delete('/delete/{id}', 'deleteData');
    });
    Route::prefix('nilai')->controller(SawController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::get('/hasil', 'hasilSaw');
        Route::post('/create', 'batchStore');
        Route::get('/data', 'getRankingResult');
        Route::post('/simpan-hasil', 'simpanHasilSaw');
        Route::delete('/clear-penilaian', 'clearPenilaian');
    });
});
