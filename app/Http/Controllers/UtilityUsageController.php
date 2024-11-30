<?php

namespace App\Http\Controllers;

use App\Models\RentalDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RoomTypePrice;
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
            'renter_id' => 'required|integer',
            'landlord_id' => 'required|integer',
            'floor' => 'required|integer',
            'room_type_price_id' => 'required|integer',
            'water_usage' => 'required|numeric',
            'electricity_usage' => 'required|numeric',
            'description' => 'required|string',
        ]);

        DB::transaction(function () use ($validatedData) {
            // Create UtilityPrice first to get a valid utility_price_id
            $utilityPrice = UtilityPrice::create([
                'water_price' => $validatedData['water_usage'], // Assuming water_price is the same as water_usage
                'electricity_price' => $validatedData['electricity_usage'], // Assuming electricity_price is the same as electricity_usage
                'other' => 0, // Default value for 'other' usage
            ]);

            // Create RoomDetail
            $room = RoomDetail::create([
                'floor' => $validatedData['floor'],
                'utility_price_id' => $utilityPrice->id, // Use the created UtilityPrice ID
                'room_type_price_id' => $validatedData['room_type_price_id'],
                'room_number' => RoomDetail::max('room_number') + 1,
                'description' => $validatedData['description'],
                'user_id' => $validatedData['landlord_id'], // Assuming user_id is the landlord_id
                'room_code' => RoomDetail::max('room_code') + 1, // Assuming room_code is auto-incremented
            ]);

            // Create UtilityUsage
            UtilityUsage::create([
                'room_code' => $room->room_code,
                'month' => now(),
                'year' => now()->year,
                'water_usage' => $validatedData['water_usage'],
                'electricity_usage' => $validatedData['electricity_usage'],
                'other' => 0, // Default value for 'other' usage
            ]);

            // Create RentalDetail
            $rental = RentalDetail::create([
                'landlord_id' => $validatedData['landlord_id'],
                'renter_id' => $validatedData['renter_id'],
                'room_id' => $room->id,
                'start_date' => now(),
                'end_date' => now()->addMonth(),
                'is_active' => true,
            ]);
        });

        return response()->json(['message' => 'Rental details and utility usage stored successfully.'], 201);
    }
    
}

