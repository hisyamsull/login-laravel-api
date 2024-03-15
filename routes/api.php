<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/login', [UserController::class, 'login']);
Route::post('/create', [UserController::class, 'create']);
Route::get('/provincy', [UserController::class, 'getprovincy']);
Route::get('/delete', [UserController::class, 'destroy']);
Route::get('/trash', [UserController::class, 'trash']);
Route::get('/restore', [UserController::class, 'restore']);
Route::get('/user', [UserController::class, 'index'])->middleware(['auth:sanctum']);
Route::get('/logout', [UserController::class, 'logout'])->middleware(['auth:sanctum']);
