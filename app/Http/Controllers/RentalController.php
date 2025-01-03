<?php

namespace App\Http\Controllers;

use App\Models\RentalDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use App\Models\RoomTypePrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    public function setupCompleteRental(Request $request): JsonResponse
    {
        // Custom validation rule to check if room_type exists in room_type_prices for the landlord
        Validator::extend('valid_room_type', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $index = explode('.', $attribute)[1];
            $landlordId = $data['rentals'][$index]['landlord_id'] ?? null;

            if (!$landlordId) {
                return false;
            }

            return RoomTypePrice::where('landlord_id', $landlordId)
                                ->where('type', $value)
                                ->exists();
        });

        // Validate the request
        $validator = Validator::make($request->all(), [
            'rentals' => 'required|array',
            'rentals.*.landlord_id' => 'required|integer|exists:user_details,id',
            'rentals.*.renter_id' => 'required|integer|exists:user_details,id',
            'rentals.*.floor' => 'required|integer',
            'rentals.*.room_number' => 'required|integer',
            'rentals.*.water_usage' => 'required|numeric',
            'rentals.*.electricity_usage' => 'required|numeric',
            'rentals.*.room_type' => 'required|string|valid_room_type',
            'rentals.*.utility_price_id' => 'required|integer|exists:utility_prices,id',
            'rentals.*.description' => 'required|string',
        ], [
            'rentals.*.room_type.valid_room_type' => 'The selected room type is invalid for this landlord.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();
        try {
            $results = [];

            foreach ($request->rentals as $rentalData) {
                // Look up room_type_price_id based on type and landlord_id
                $roomTypePrice = RoomTypePrice::where('landlord_id', $rentalData['landlord_id'])
                                            ->where('type', $rentalData['room_type'])
                                            ->first();

                if (!$roomTypePrice) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Room type not found for this landlord',
                        'message' => "Room type '{$rentalData['room_type']}' is not configured for landlord ID {$rentalData['landlord_id']}"
                    ], 404);
                }

                // Check if room is already taken
                $existingRoom = RoomDetail::where([
                    'floor' => $rentalData['floor'],
                    'room_number' => $rentalData['room_number'],
                    'available' => false
                ])->first();

                if ($existingRoom) {
                    DB::rollBack();
                    return response()->json([
                        'error' => 'Room already occupied',
                        'message' => "Room {$rentalData['room_number']} on floor {$rentalData['floor']} is already taken"
                    ], 400);
                }

                // 1. Setup Room
                $roomCode = $this->generateRoomCode();
                $room = RoomDetail::updateOrCreate(
                    [
                        'user_id' => $rentalData['landlord_id'],
                        'floor' => $rentalData['floor'],
                        'room_number' => $rentalData['room_number']
                    ],
                    [
                        'utility_price_id' => $rentalData['utility_price_id'],
                        'room_type_price_id' => $roomTypePrice->id,
                        'room_code' => $roomCode,
                        'description' => $rentalData['description'],
                        'available' => false
                    ]
                );

                // 2. Create Rental Record first
                $rental = RentalDetail::create([
                    'landlord_id' => $rentalData['landlord_id'],
                    'renter_id' => $rentalData['renter_id'],
                    'room_id' => $room->id,
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                    'is_active' => true,
                ]);

                // 3. Create Utility Usage Record with rental_id
                $utilityUsage = UtilityUsage::create([
                    'rental_id' => $rental->id,  // Add the rental_id here
                    'room_code' => $roomCode,
                    'water_usage' => $rentalData['water_usage'],
                    'electricity_usage' => $rentalData['electricity_usage'],
                    'other' => $rentalData['other'] ?? 0,
                ]);

                $results[] = [
                    'room' => $room,
                    'utility' => $utilityUsage,
                    'rental' => $rental,
                    'room_type' => $roomTypePrice
                ];
            }

            DB::commit();

            return response()->json([
                'message' => 'Multiple rental setups completed successfully',
                'data' => $results
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to setup rentals',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique room code.
     *
     * @return string
     */
    private function generateRoomCode(): string
    {
        $code = mt_rand(100000, 999999);
        while (RoomDetail::where('room_code', $code)->exists()) {
            $code = mt_rand(100000, 999999);
        }
        return (string) $code;
    }
}