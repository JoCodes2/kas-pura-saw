<?php

use App\Http\Controllers\CMS\KasmasukController;
use App\Http\Controllers\CMS\KegiatanController;
use App\Http\Controllers\CMS\KriteriaController;
use App\Http\Controllers\CMS\MasterController;
use Illuminate\Support\Facades\Route;

Route::get('/pengguna', function () {
    return view('Admin.pengguna');
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

Route::get('/', function () {
    return view('Ui.utama');
});

Route::prefix('saw')->group(function () {

    // lokasi kantor
    Route::prefix('master')->controller(MasterController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::post('/create', 'createData');
        Route::get('/get/{id}', 'getDataById');
        Route::post('/update/{id}', 'updateData');
        Route::delete('/delete/{id}', 'deleteData');
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
    });

    Route::prefix('kriteria')->controller(KriteriaController::class)->group(function () {
        Route::get('/', 'getAllData');
        Route::post('/create', 'createData');
        Route::get('/get/{id}', 'getDataById');
        Route::post('/update/{id}', 'updateData');
        Route::delete('/delete/{id}', 'deleteData');
    });
});
