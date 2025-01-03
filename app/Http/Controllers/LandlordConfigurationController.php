<?php

namespace App\Http\Controllers;

use App\Models\UtilityPrice;
use App\Models\LandlordFloorRoom;
use App\Models\RoomTypePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class LandlordConfigurationController extends Controller
{
    /**
     * Store or update landlord configurations including utility prices, floors, and room types
     */
    public function storeLandlordConfigurations(Request $request)
    {
        try {
            // Validate all incoming data
            $validated = $request->validate([
                'landlord_id' => 'required|exists:user_details,id',
                
                // Utility prices validation
                'water_price' => 'required|numeric|min:0',
                'electricity_price' => 'required|numeric|min:0',
                
                // Floors validation
                'floors' => 'required|array|min:1',
                'floors.*.floor_number' => 'required|integer|min:1',
                'floors.*.room_count' => 'required|integer|min:1',
                
                // Room types validation
                'room_types' => 'required|array',
                'room_types.*.type' => 'required|string',
                'room_types.*.price' => 'required|integer',
            ]);

            // Begin transaction
            DB::beginTransaction();

            // 1. Handle Utility Prices
            $utilityPrice = UtilityPrice::updateOrCreate(
                ['landlord_id' => $validated['landlord_id']],
                [
                    'water_price' => $validated['water_price'],
                    'electricity_price' => $validated['electricity_price'],
                ]
            );

            // 2. Handle Floor and Room Configuration
            // Delete existing floor data
            LandlordFloorRoom::where('landlord_id', $validated['landlord_id'])->delete();
            
            // Create new floor data
            $savedFloors = [];
            foreach ($validated['floors'] as $floor) {
                $floorRoom = LandlordFloorRoom::create([
                    'landlord_id' => $validated['landlord_id'],
                    'floor' => $floor['floor_number'],
                    'room' => $floor['room_count'],
                ]);
                $savedFloors[] = $floorRoom;
            }

            // 3. Handle Room Types and Prices
            // Delete existing room types
            RoomTypePrice::where('landlord_id', $validated['landlord_id'])->delete();
            
            // Create new room types
            $savedRoomTypes = [];
            foreach ($validated['room_types'] as $roomType) {
                $roomTypePrice = RoomTypePrice::create([
                    'landlord_id' => $validated['landlord_id'],
                    'type' => $roomType['type'],
                    'type_price' => $roomType['price'],
                ]);
                $savedRoomTypes[] = $roomTypePrice;
            }

            // Commit transaction
            DB::commit();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Landlord configurations saved successfully',
                'data' => [
                    'utility_prices' => $utilityPrice,
                    'floors' => $savedFloors,
                    'room_types' => $savedRoomTypes,
                ]
            ], 201);

        } catch (Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}