<?php

namespace App\Http\Controllers;

use App\Models\RentalDetail;
use App\Models\RoomDetail;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    public function setupRental(Request $request): JsonResponse
    {
        // Validate the input data
        $validator = Validator::make($request->all(), [
            'landlord_id' => 'required|integer|exists:user_details,id', // Ensure landlord exists
            'renter_id' => 'required|integer|exists:user_details,id',   // Ensure renter exists
            'room_id' => 'required|integer|exists:room_details,id',     // Ensure room exists
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',             // Ensure end_date is after start_date
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Fetch the room to ensure it is available
        $room = RoomDetail::where('id', $request->room_id)
                          ->where('available', true)
                          ->first();

        if (!$room) {
            return response()->json(['error' => 'The room is either not available or does not exist'], 404);
        }

        // Create the rental detail
        $rental = RentalDetail::create([
            'landlord_id' => $request->landlord_id,
            'renter_id' => $request->renter_id,
            'room_id' => $request->room_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
        ]);

        // Mark the room as unavailable after rental setup
        $room->update(['available' => false]);

        // Return the created rental as a JSON response
        return response()->json($rental, 201);
    }
}
