<?php

use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/index', [Api::class, 'index'])->name('index');
