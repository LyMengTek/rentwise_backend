<?php

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\RoomController;

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


Route::Get('invoice', [InvoiceDetailController::class, 'index']);
Route::Post('create/invoice', [InvoiceDetailController::class, 'store']);

Route::post('/register', [UserDetailController::class, 'register']);
Route::get('/user/{id}', [UserDetailController::class, 'showUser']);
Route::post('/login', [UserDetailController::class, 'login']);

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/available', [RoomController::class, 'available']);
route::get('/rooms/available/join-code', [RoomController::class, 'availableByJoinCode']);