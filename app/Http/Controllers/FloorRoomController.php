<?php

namespace App\Http\Controllers;

use App\Models\LandlordFloorRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Import DB facade

class FloorRoomController extends Controller
{
    // Function to create or update multiple floor and room entries for a landlord
    public function storeFloorRoom(Request $request)
    {
        try {
            // Validate incoming request data
            $validated = $request->validate([
                'landlord_id' => 'required|exists:user_details,id',
                'floors' => 'required|array|min:1',
                'floors.*.floor_number' => 'required|integer|min:1',
                'floors.*.room_count' => 'required|integer|min:1',
            ]);

            $landlordId = $validated['landlord_id'];
            $floors = $validated['floors'];

            // Begin a database transaction to ensure atomicity
            DB::beginTransaction(); // Correct use of DB facade

            // Check if the landlord already has floor data
            LandlordFloorRoom::where('landlord_id', $landlordId)->delete(); // Delete existing floor data

            // Create or update new floor data for the landlord
            $savedData = [];
            foreach ($floors as $floor) {
                $floorRoom = LandlordFloorRoom::create([
                    'landlord_id' => $landlordId,
                    'floor' => $floor['floor_number'],
                    'room' => $floor['room_count'],
                ]);

                $savedData[] = $floorRoom;
            }

            // Commit the transaction
            DB::commit(); // Correct use of DB facade

            // Return success response with saved data
            return response()->json([
                'message' => 'Floor and room details saved successfully',
                'data' => $savedData,
            ]);

        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack(); // Correct use of DB facade

            // Return error response with exception message
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
