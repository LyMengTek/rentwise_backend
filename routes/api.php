<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UtillityController;
use App\Models\UserDetail;

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

Route::get('/landlord/{landlord_id}/renters', [UserDetail::class, 'getRentersByJoinCode']);
route::post('/rooms/by-join-code', [UserDetail::class, 'getAvailableRoomsByJoinCode']);

Route::get('/rooms/available', [RoomController::class, 'getAllAvailableRooms']);

Route::Get('invoice', [InvoiceDetailController::class, 'index']);
Route::Post('create/invoice', [InvoiceDetailController::class, 'store']);

Route::post('/register', [UserDetailController::class, 'register']);
Route::get('/user/{id}', [UserDetailController::class, 'showUser']);
Route::post('/login', [UserDetailController::class, 'login']);


Route::get('/rooms/available', [RoomController::class, 'getAvailableRooms']);
route::get('/rooms/available/{id}', [RoomController::class, 'getAvailableRoomsByUserId']);
Route::post('/rooms/setup', [RoomController::class, 'setupRoom']);

Route::post('/utillity/create', [UtillityController::class, 'createUtility']);
Route::post('/rential/setup', [RentalController::class, 'setupRental']);

Route::post('/create/invoice', [InvoiceController::class, 'createInvoice']);
