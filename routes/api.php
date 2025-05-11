<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HourlyController;

Route::post('/hourly/store', [HourlyController::class, 'store']);
Route::get('/hourly', [HourlyController::class, 'index']);
