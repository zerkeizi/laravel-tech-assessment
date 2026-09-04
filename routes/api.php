<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\PayableController;
use App\Http\Controllers\Api\ReceivableController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    Route::apiResource('parties', PartyController::class);

    Route::apiResource('payables', PayableController::class);
    Route::patch('payables/{payable}/pay', [PayableController::class, 'pay']);

    Route::apiResource('receivables', ReceivableController::class);
    Route::patch('receivables/{receivable}/receive', [ReceivableController::class, 'receive']);

    Route::get('/dashboard', DashboardController::class);
    Route::get('/reports', ReportController::class);
});
