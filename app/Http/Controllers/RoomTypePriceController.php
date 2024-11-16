<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomTypePrice;
use Illuminate\Database\QueryException;
use Exception;

class RoomTypePriceController extends Controller
{
    /**
     * Store new RoomTypePrices for each type provided.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'landlord_id' => 'required|exists:user_details,id',
                'room_types' => 'required|array',
                'room_types.*.type' => 'required|string',
                'room_types.*.price' => 'required|integer',
            ]);
    
            // Array to store created RoomTypePrice entries
            $roomTypePrices = [];
    
            // Iterate over each room type and create a RoomTypePrice entry
            foreach ($validatedData['room_types'] as $roomType) {
                $roomTypePrice = RoomTypePrice::create([
                    'landlord_id' => $validatedData['landlord_id'],
                    'type' => $roomType['type'],
                    'type_price' => $roomType['price'],
                ]);
    
                // Collect the created entry details
                $roomTypePrices[] = [
                    'id' => $roomTypePrice->id,
                    'landlord_id' => $roomTypePrice->landlord_id,
                    'type' => $roomTypePrice->type,
                    'type_price' => $roomTypePrice->type_price,
                    'created_at' => $roomTypePrice->created_at,
                    'updated_at' => $roomTypePrice->updated_at,
                ];
            }
    
            // Return the details of all created RoomTypePrice entries
            return response()->json([
                'success' => true,
                'room_type_prices' => $roomTypePrices,
                'message' => 'Room type prices created successfully.',
            ], 201);
        } catch (QueryException $e) {
            // Handle database-related exceptions
            return response()->json([
                'success' => false,
                'error' => 'Database error',
                'message' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            // Handle general exceptions
            return response()->json([
                'success' => false,
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
