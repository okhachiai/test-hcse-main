<?php

use App\Http\Controllers\Api\OfferController as ApiOfferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

// Public endpoint to list published offers (rate limited: 60 req/min per IP)
Route::get('/offers', new ApiOfferController()->index(...))->middleware('throttle:60,1');
