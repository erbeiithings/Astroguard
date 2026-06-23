<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AstroController;

// Arahin halaman utama ke AstroController
Route::get('/', [AstroController::class, 'index']);