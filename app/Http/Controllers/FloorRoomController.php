<?php

namespace App\Http\Controllers;

use App\Models\LandlordFloorRoom;
use Illuminate\Http\Request;

class FloorRoomController extends Controller
{
    // Function to create or update multiple floor and room entries for a landlord
    public function storeFloorRoom(Request $request)
    {
        try {
            $validated = $request->validate([
                'landlord_id' => 'required|exists:user_details,id',
                'floors' => 'required|array|min:1',
                'floors.*.floor_number' => 'required|integer|min:1',
                'floors.*.room_count' => 'required|integer|min:1',
            ]);

            $landlordId = $validated['landlord_id'];
            $floors = $validated['floors'];

            $savedData = [];
            foreach ($floors as $floor) {
                $floorRoom = LandlordFloorRoom::updateOrCreate(
                    ['landlord_id' => $landlordId, 'floor' => $floor['floor_number']],
                    ['rooms' => $floor['room_count']]
                );

                $savedData[] = $floorRoom;
            }

            return response()->json([
                'message' => 'Floor and room details saved successfully',
                'data' => $savedData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}