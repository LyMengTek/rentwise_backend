<?php

namespace App\Http\Controllers;

use App\Models\RentalDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



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
        $data = $request->all(); // Assuming data is already an array of entries
        
        $response = [];

        foreach ($data as $entry) {
            try {
                // Create or retrieve the room
                $room = RoomDetail::firstOrCreate(
                    [
                        'floor' => $entry['floor'],
                        'room_number' => $entry['room_number'],
                        'user_id' => $entry['landlord_id'], // Landlord as user
                        'room_type' => $entry['room_type_id'],
                    ],
                    [
                        'description' => $entry['description'],
                        'available' => true, // Default availability
                        'utility_price' => null, // Assuming no utility price for now
                    ]
                );

                // Create the rental detail
                $rental = RentalDetail::create([
                    'landlord_id' => $entry['landlord_id'],
                    'renter_id' => $entry['renter_id'],
                    'room_id' => $room->id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                    'is_active' => true,
                ]);

                // Create the utility usage
                $utility = UtilityUsage::create([
                    'room_code' => $room->id,
                    'month' => now(),
                    'year' => now()->year,
                    'water_usage' => $entry['water_usage'],
                    'electricity_usage' => $entry['electricity_usage'],
                    'other' => 0, // Default value
                ]);

                $response[] = [
                    'rental_id' => $rental->id,
                    'room_id' => $room->id,
                    'utility_id' => $utility->id,
                    'message' => 'Rental and utility details saved successfully.',
                ];
            } catch (\Exception $e) {
                $response[] = [
                    'error' => $e->getMessage(),
                    'data' => $entry,
                ];
            }
        }

        return response()->json($response);
    }
}

