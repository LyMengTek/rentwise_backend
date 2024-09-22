<?php

namespace App\Http\Controllers;

use App\Models\LandlordDetail;
use App\Models\RoomDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Function to get all room details
    public function index(): JsonResponse
    {
        $rooms = RoomDetail::all();
        return response()->json($rooms);
    }

    // Function to get only available rooms
    public function available(): JsonResponse
    {
        $availableRooms = RoomDetail::available()->get();
        return response()->json($availableRooms);
    }

    // Function to get available rooms by landlord join code
    public function availableByJoinCode(Request $request): JsonResponse
    {
        $request->validate(['join_code' => 'required|integer']);

        // Find the landlord by join code
        $landlord = LandlordDetail::where('join_code', $request->join_code)->first();

        if (!$landlord) {
            return response()->json(['message' => 'Landlord not found.'], 404);
        }

        // Get available rooms for the landlord
        $availableRooms = RoomDetail::available()->where('landlord_id', $landlord->id)->get();

        return response()->json($availableRooms);
    }
}
