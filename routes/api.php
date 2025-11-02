<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventApiController;

Route::get('/events', [EventApiController::class, 'index']);
Route::post('/events', [EventApiController::class, 'store']);
Route::get('/events/{id}', [EventApiController::class, 'show']);
Route::put('/events/{id}', [EventApiController::class, 'update']);
Route::delete('/events/{id}', [EventApiController::class, 'destroy']);

