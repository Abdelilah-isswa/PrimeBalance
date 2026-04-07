<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;

Route::middleware('auth:sanctum')->get('/companies', [CompanyController::class, 'index']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
