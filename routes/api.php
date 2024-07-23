<?php

use App\Http\Controllers\Api\PurchaseController;
use Illuminate\Support\Facades\Route;


Route::get('/sales_data', [PurchaseController::class, 'index']);
Route::get('/sales_data/{id}', [PurchaseController::class, 'show']);
