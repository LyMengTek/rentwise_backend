<?php

namespace App\Http\Controllers;

use App\Models\LandlordDetail;
use App\Models\RoomDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function getAvailableRooms(): JsonResponse
    {
        // Fetch all rooms that are available
        $availableRooms = RoomDetail::available()->get();

        // Return as JSON response
        return response()->json($availableRooms);
    }

    public function getAvailableRoomsByUserId($user_id): JsonResponse
    {
        // Fetch all available rooms that belong to the specified landlord
        $availableRooms = RoomDetail::where('landlord_id', $user_id)
                                    ->available() // Uses the scopeAvailable method
                                    ->get();

        // Return as JSON response
        return response()->json($availableRooms);
    }

    public function setupRoom(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'landlord_id' => 'required|integer',
            'floor' => 'required|integer',
            'room_number' => 'required|string',
            'water_price' => 'required|numeric',
            'electricity_price' => 'required|numeric',
            'room_price' => 'required|numeric',
            'available' => 'required|boolean',
            'utility_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create or update room details
        $room = RoomDetail::updateOrCreate(
            [
                'landlord_id' => $request->user_id,
                'room_number' => $request->room_number,
                'utility_id' =>  $request->utility_id,
            ], // Match on landlord_id and room_number to allow updates
            [
                'floor' => $request->floor,
                'water_price' => $request->water_price,
                'electricity_price' => $request->electricity_price,
                'room_price' => $request->room_price,
                'available' => $request->available,
            ]
        );

        // Return the created/updated room as a JSON response
        return response()->json($room, 201);
    }
}
