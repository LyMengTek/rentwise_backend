<?php

namespace App\Http\Controllers;

use App\Models\RoomDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    // Fetch all available rooms
    public function getAvailableRooms(): JsonResponse
    {
        $availableRooms = RoomDetail::available()->get();

        return response()->json($availableRooms);
    }

    // Fetch available rooms by landlord/user_id
    public function getAvailableRoomsByUserId($user_id): JsonResponse
    {
        $availableRooms = RoomDetail::where('user_id', $user_id)
                                    ->available()
                                    ->get();

        return response()->json($availableRooms);
    }

    public function setupRoom(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'rooms' => 'required|array', // Expect an array of rooms
            'rooms.*.user_id' => 'required|integer',
            'rooms.*.floor' => 'required|integer',
            'rooms.*.room_number' => 'required|integer',
            'rooms.*.utility_price_id' => 'required|integer|exists:utility_prices,id', // Ensure utility_price_id exists
            'rooms.*.room_type_price_id' => 'required|integer|exists:room_type_prices,id', // Ensure room_type_price_id exists
            'rooms.*.room_code' => 'required|string',
            'rooms.*.description' => 'required|string',
            'rooms.*.available' => 'boolean' // Optional, defaults to true
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $createdRooms = [];
    
        // Loop through each room setup
        foreach ($request->rooms as $roomData) {
            // Create or update room details
            $room = RoomDetail::updateOrCreate(
                [
                    'user_id' => $roomData['user_id'],
                    'room_number' => $roomData['room_number'],
                ],
                [
                    'floor' => $roomData['floor'],
                    'utility_price_id' => $roomData['utility_price_id'],
                    'room_type_price_id' => $roomData['room_type_price_id'],
                    'room_code' => $roomData['room_code'],
                    'description' => $roomData['description'],
                    'available' => isset($roomData['available']) ? $roomData['available'] : true, // Default to true
                ]
            );
    
            // Add the created/updated room to the response
            $createdRooms[] = $room;
        }
    
        // Return the created/updated rooms as a JSON response
        return response()->json($createdRooms, 201);
    }
    
    // Generate a unique 6-digit room code
    private function generateRoomCode(): int
    {
        do {
            // Generate a random 6-digit code
            $code = mt_rand(100000, 999999);
        } while (RoomDetail::where('room_code', $code)->exists());

        return $code;
    }

    public function getAllAvailableRooms(): JsonResponse
    {
        // Fetch all rooms that are available
        $availableRooms = RoomDetail::available()->get();

        // Check if any rooms are available
        if ($availableRooms->isEmpty()) {
            return response()->json(['message' => 'No available rooms found'], 200);
        }

        // Return the available rooms as a JSON response
        return response()->json($availableRooms, 200);
    }
}
