<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/api/profile', \App\Http\Controllers\ProfileController::class);
    Route::get('/api/order', \App\Http\Controllers\ListOrderController::class);
    Route::post('/api/order', \App\Http\Controllers\CreateOrderController::class);
    Route::post('/api/order/{order}/cancel', \App\Http\Controllers\CancelOrderController::class);

});
