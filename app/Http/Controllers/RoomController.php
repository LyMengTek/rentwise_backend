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

    // Create or update room details
    public function setupRoom(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'floor' => 'required|integer',
            'room_number' => 'required|string',
            'water_price' => 'required|numeric',
            'electricity_price' => 'required|numeric',
            'room_price' => 'required|numeric',
            'available' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Generate a unique room code if not provided
        $roomCode = $request->has('room_code') ? $request->room_code : $this->generateRoomCode();

        // Create or update room details
        $room = RoomDetail::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'room_number' => $request->room_number,
            ],
            [
                'floor' => $request->floor,
                'water_price' => $request->water_price,
                'electricity_price' => $request->electricity_price,
                'room_price' => $request->room_price,
                'available' => $request->available,
                'room_code' => $roomCode,
            ]
        );

        // Return the created/updated room as a JSON response
        return response()->json($room, 201);
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
