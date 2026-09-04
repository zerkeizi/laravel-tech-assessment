<?php

use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\PayableController;
use App\Http\Controllers\Api\ReceivableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('parties', PartyController::class);

Route::apiResource('payables', PayableController::class);

Route::apiResource('receivables', ReceivableController::class);

