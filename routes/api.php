<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Rutas protegidas por JWT
Route::middleware('jwt.auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/cart', [CartItemController::class, 'addToCart']);
    Route::get('/cart', [CartItemController::class, 'getCartItems']);
    Route::delete('/cart/{id}', [CartItemController::class, 'removeFromCart']);
});


