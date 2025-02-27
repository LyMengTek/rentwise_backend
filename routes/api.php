<?php

use App\Http\Controllers\FloorRoomController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\LandlordConfigurationController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomSetupController;
use App\Http\Controllers\RoomTypePriceController;
use App\Http\Controllers\UtilityPriceController;
use App\Http\Controllers\UtilityUsageController;
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
Route::get('invoice/byRenter/{renter_id}', [InvoiceDetailController::class, 'getByRenterId']);
Route::get('invoice/byLandlord/{landlord_id}', [InvoiceDetailController::class, 'getByLandlordId']);
Route::get('contact/byLandlord/{landlord_id}', [InvoiceDetailController::class, 'getContact']);

Route::post('/register', [UserDetailController::class, 'register']);
Route::get('/user/{id}', [UserDetailController::class, 'showUser']);
Route::post('/login', [UserDetailController::class, 'login']);


Route::get('/rooms/available', [RoomController::class, 'getAvailableRooms']);
route::get('/rooms/available/{id}', [RoomController::class, 'getAvailableRoomsByUserId']);
Route::post('/rooms/setup', [RoomController::class, 'setupRoom']);

Route::post('/utillity/create', [UtillityController::class, 'createUtility']);

Route::post('/utility-usage', [UtilityUsageController::class, 'storeUtility'])->name('utility-usage.storeUtility');

//big 2
Route::get('/landlord/{landlordId}/rooms', [LandlordConfigurationController::class, 'generateRoomsByLandlordId']);
Route::post('/rental/setup', [RentalController::class, 'setupCompleteRental']);
Route::post('/landlord-configurations', [LandlordConfigurationController::class, 'storeLandlordConfigurations']);

Route::post('/create/invoice', [InvoiceController::class, 'createInvoice']);

Route::post('/utility-prices', [UtilityPriceController::class, 'storeUtilityPrices']);
Route::post('/landlord-floor-rooms', [FloorRoomController::class, 'storeFloorRoom']);

Route::post('/room-type-prices', [RoomTypePriceController::class, 'store']);


Route::post('/setup', [UtilityUsageController::class, 'storeRentalDetails']);


Route::get('/room-details/{landlordId}', [RoomSetupController::class, 'getRoomAndFloorDetails'])->name('landlord.room.details');

Route::get('/rentals/{id}', [RentalController::class, 'showRentalsByLandlord']);