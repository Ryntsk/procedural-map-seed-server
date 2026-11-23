<?php

use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('maps.index'));

Route::get('/maps', [MapController::class, 'index'])->name('maps.index');
Route::get('/maps/search', [MapController::class, 'search'])->name('maps.search');
Route::get('/maps/{seed}', [MapController::class, 'show'])->name('maps.show');