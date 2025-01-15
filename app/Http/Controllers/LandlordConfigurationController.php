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


    function generateRoomsByLandlordId($landlordId): array
    {
        // Fetch floors and room counts for the landlord
        $floors = LandlordFloorRoom::where('landlord_id', $landlordId)
            ->get(['floor', 'room']);

        // Fetch room types for the landlord
        $roomTypes = RoomTypePrice::where('landlord_id', $landlordId)
            ->pluck('type')
            ->toArray();

        // Fetch the first utility price ID for the landlord
        $utilityPrice = UtilityPrice::where('landlord_id', $landlordId)
            ->first(); // Get the first utility price record

        // If no floors, room types, or utility price is found, return an empty array
        if ($floors->isEmpty() || empty($roomTypes) || !$utilityPrice) {
            return [
                'room_types' => [],
                'rooms' => [],
                'utility_price_id' => null, // Return null if no utility price is found
            ];
        }

        $rooms = [];

        // Loop through each floor
        foreach ($floors as $floor) {
            $floorNumber = $floor->floor;
            $roomCount = $floor->room;

            // Generate rooms for the current floor
            for ($roomNumber = 1; $roomNumber <= $roomCount; $roomNumber++) {
                $rooms[] = [
                    'floor' => $floorNumber,
                    'room' => $roomNumber,
                ];
            }
        }

        return [
            'room_types' => $roomTypes,
            'rooms' => $rooms,
            'utility_price_id' => (int)$utilityPrice->id, // Return a single integer
        ];
    }


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
                'room_types.*.price' => 'required',
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

            $roomTypes = array_map(function ($roomType) {
                return $roomType->type;
            }, $savedRoomTypes);

            $rooms = [];
            foreach ($savedFloors as $floor) {
                for ($i = 1; $i <= $floor->room; $i++) {
                    $rooms[] = [
                        'floor' => $floor->floor,
                        'room' => $i
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'room_types' => $roomTypes,
                    'rooms' => $rooms
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
