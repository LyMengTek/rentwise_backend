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
        // Validate the request
        $validator = Validator::make($request->all(), [
            'rentals' => 'required|array', // Expect an array of rentals
            'rentals.*.landlord_id' => 'required|integer|exists:user_details,id', // Ensure landlord exists
            'rentals.*.renter_id' => 'required|integer|exists:user_details,id',   // Ensure renter exists
            'rentals.*.room_id' => 'required|integer|exists:room_details,id',     // Ensure room exists
            'rentals.*.start_date' => 'required|date',
            'rentals.*.end_date' => 'required|date|after:rentals.*.start_date',   // Ensure end_date is after start_date
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        $createdRentals = [];
    
        // Loop through each rental setup
        foreach ($request->rentals as $rentalData) {
            // Fetch the room to ensure it is available
            $room = RoomDetail::where('id', $rentalData['room_id'])
                ->where('available', true)
                ->first();
    
            if (!$room) {
                return response()->json(['error' => 'The room with ID ' . $rentalData['room_id'] . ' is either not available or does not exist'], 404);
            }
    
            // Create the rental detail
            $rental = RentalDetail::create([
                'landlord_id' => $rentalData['landlord_id'],
                'renter_id' => $rentalData['renter_id'],
                'room_id' => $rentalData['room_id'],
                'start_date' => $rentalData['start_date'],
                'end_date' => $rentalData['end_date'],
                'is_active' => true,
            ]);
    
            // Mark the room as unavailable after rental setup
            $room->update(['available' => false]);
    
            // Add the created rental to the response
            $createdRentals[] = $rental;
        }
    
        // Return the created rentals as a JSON response
        return response()->json($createdRentals, 201);
    }
}
