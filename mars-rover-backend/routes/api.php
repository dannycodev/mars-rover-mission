<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoverController;

// Rutas para el rover
Route::post('/rover/move', [RoverController::class, 'move']);
