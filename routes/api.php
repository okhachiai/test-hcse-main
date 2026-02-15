<?php

use App\Http\Controllers\Api\OfferController as ApiOfferController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

// Public endpoints (rate limited: 60 req/min per IP via named limiter 'api')
Route::middleware('throttle:api')->group(function () {
    Route::get('/offers', new ApiOfferController()->index(...));
});
