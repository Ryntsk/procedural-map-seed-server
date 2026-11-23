<?php

use App\Http\Controllers\Api\MapApiController;
use Illuminate\Support\Facades\Route;

Route::get('/maps/{seed}', [MapApiController::class, 'show'])->name('api.maps.show');