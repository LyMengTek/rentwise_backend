<?php

namespace App\Http\Controllers;

use App\Models\UtilityUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UtillityController extends Controller
{
    public function createUtility(Request $request): JsonResponse
{
    // Validate the request input
    $validator = Validator::make($request->all(), [
        'month' => 'required|date', // Ensure the month is passed as a date
        'year' => 'required|integer',
        'water_usage' => 'required|numeric',
        'electricity_usage' => 'required|numeric',
        'other' => 'nullable|numeric', // 'other' is optional
        'room_code' => 'nullable|integer', // Allow room_code to be optional
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Check if room_code was provided
    if ($request->filled('room_code')) {
        $roomCode = $request->room_code; // Use provided room_code
    } else {
        // If not provided, generate a unique room_code
        $roomCode = $this->generateUniqueRoomCode();
    }

    // Create the utility usage record
    $utility = UtilityUsage::create([
        'month' => $request->month,
        'year' => $request->year,
        'water_usage' => $request->water_usage,
        'electricity_usage' => $request->electricity_usage,
        'other' => $request->other ?? 0, // Default to 0 if not provided
        'room_code' => $roomCode, // Use either provided or generated room_code
    ]);

    // Return the created utility usage as a JSON response
    return response()->json($utility, 201);
}

// Helper method to generate a unique room code
private function generateUniqueRoomCode()
{
    do {
        $code = mt_rand(100000, 999999); // Generate a random 6-digit number
    } while (UtilityUsage::where('room_code', $code)->exists());

    return $code;
}


}
