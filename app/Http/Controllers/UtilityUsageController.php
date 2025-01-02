<?php

namespace App\Http\Controllers;

use App\Models\RentalDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RoomTypePrice;
use Illuminate\Support\Str;
use App\Models\UtilityPrice; // Add this import

class UtilityUsageController extends Controller
{
    /**
     * Store rental and utility usage details.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeRentalDetails(Request $request)
    {
        $validatedData = $request->validate([
            'renter_id' => 'nullable|integer',
            'landlord_id' => 'required|integer',
            'floor' => 'required|integer',
            'room_type_price_id' => 'required|integer',
            'water_usage' => 'required|numeric',
            'electricity_usage' => 'required|numeric',
            'description' => 'required|string',
        ]);

        // Declare the variables outside the transaction
        $room = null;
        $utilityUsage = null;
        $rental = null;
        $utilityPrice = null;

        try {
            DB::transaction(function () use ($validatedData, &$room, &$utilityUsage, &$rental, &$utilityPrice) {
                // Create UtilityPrice first to get a valid utility_price_id
                $utilityPrice = UtilityPrice::create([
                    'landlord_id' => $validatedData['landlord_id'],
                    'water_price' => $validatedData['water_usage'], // Assuming water_price is the same as water_usage
                    'electricity_price' => $validatedData['electricity_usage'], // Assuming electricity_price is the same as electricity_usage
                    'other' => 0, // Default value for 'other' usage
                ]);

                // Create RoomDetail
                $room = RoomDetail::create([
                    'floor' => $validatedData['floor'],
                    'user_id' => $validatedData['landlord_id'],
                    'room_number' => $this->generateRoomNumber($validatedData['floor']),
                    'available' => true,
                    'room_code' => $this->generateRoomCode(),
                    'description' => $validatedData['description'],
                    'room_type_price_id' => $validatedData['room_type_price_id'],
                    'utility_price_id' => $utilityPrice->id,
                ]);

                // Create RentalDetail
                $rental = RentalDetail::create([
                    'landlord_id' => $validatedData['landlord_id'],
                    'renter_id' => $validatedData['renter_id'] ?? null, // Handle missing renter_id
                    'room_id' => $room->id,
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                    'is_active' => true,
                ]);

                // Create UtilityUsage
                $utilityUsage = UtilityUsage::create([
                    'rental_id' => $rental->id, // Ensure this field is set
                    'room_code' => $room->room_code,
                    'water_usage' => $validatedData['water_usage'],
                    'electricity_usage' => $validatedData['electricity_usage'],
                    'other' => 0, // Default value for 'other' usage
                ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Rental details stored successfully.',
                'data' => [
                    'room' => $room,
                    'utility_usage' => $utilityUsage,
                    'rental' => $rental,
                    'utility_price' => $utilityPrice,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while storing rental details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeUtility(Request $request)
    {
        // Validate the request data
        $request->validate([
            '*.water_usage' => 'required|numeric',
            '*.electricity_usage' => 'required|numeric',
            '*.other' => 'required|numeric',
        ]);
        
        // Initialize an array to store all the utility usage records
        $utilityUsageRecords = [];
    
        // Loop through the request data and store each utility usage
        foreach ($request->all() as $utilityData) {
            // Generate a 6-digit room code for each entry
            $roomCode = $this->generateRoomCode();
            
            // Create the UtilityUsage record for each utility data object
            $utilityUsageRecord = UtilityUsage::create([
                'room_code' => $roomCode,
                'water_usage' => $utilityData['water_usage'] ?? 0,
                'electricity_usage' => $utilityData['electricity_usage'] ?? 0,
                'other' => $utilityData['other'] ?? 0,
            ]);
    
            // Add the created record to the array of all records
            $utilityUsageRecords[] = $utilityUsageRecord;
        }
        
        // Return response with success message and all stored records
        return response()->json([
            'message' => 'Utility usage stored successfully.',
            'data' => $utilityUsageRecords,
        ], 201);
    }
    
    /**
     * Generate a 6-digit room code.
     *
     * @return string
     */
    private function generateRoomNumber($floor)
    {
        // Generate a unique room number based on the floor
        return $floor * 100 + rand(1, 99);
    }

    private function generateRoomCode()
    {
        // Generate a unique room code
        return rand(100000, 999999);
    }
    

    
}

