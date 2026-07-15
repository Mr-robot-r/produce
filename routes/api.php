<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\VoucherController;

Route::post('/vouchers/confirm', [VoucherController::class, 'confirm']);
Route::post('/vouchers/cancel', [VoucherController::class, 'cancel']);
Route::get('/products/{productId}/warehouses/{warehouseId}/history', [VoucherController::class, 'history']);
Route::get('/products/{productId}/bom-cost', [VoucherController::class, 'bomCost']);
Route::get('/bom/{productId}/tree', [VoucherController::class, 'bomTree']);