<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/', [EventController::class, 'index'])->name('events.index');
Route::get('/admin', [EventController::class, 'admin'])->name('events.admin');
Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
