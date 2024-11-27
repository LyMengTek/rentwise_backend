<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use App\Models\RoomDetail;
use App\Models\UtilityPrice;
use App\Models\UtilityUsage;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'user_id' => 'required|integer|exists:user_details,id', // Validate user_id
            'new_water_usage' => 'required|numeric',
            'new_electricity_usage' => 'required|numeric',
            'other' => 'required|numeric',
            'room_code' => 'required|integer',
        ]);

        $userId = $request->input('user_id');
        $newWaterUsage = $request->input('new_water_usage');
        $newElectricityUsage = $request->input('new_electricity_usage');
        $other = $request->input('other');
        $roomCode = $request->input('room_code');

        // Find the current utility usage for the given room code
        $currentUsage = UtilityUsage::where('room_code', $roomCode)
            ->latest('created_at')
            ->first();

        if (!$currentUsage) {
            return response()->json(['error' => 'Utility usage not found for the specified room code.'], 404);
        }

        // Find room details
        $room = RoomDetail::where('room_code', $roomCode)->first();
        if (!$room) {
            return response()->json(['error' => 'Room not found.'], 404);
        }

        // Retrieve utility prices using utility_price_id
        $utilityPrice = UtilityPrice::find($room->utility_price_id);
        if (!$utilityPrice) {
            return response()->json(['error' => 'Utility price details not found.'], 404);
        }

        // Calculate old usage
        $oldWaterUsage = $currentUsage->water_usage;
        $oldElectricityUsage = $currentUsage->electricity_usage;

        // Calculate usage differences
        $waterDifference = $newWaterUsage - $oldWaterUsage;
        $electricityDifference = $newElectricityUsage - $oldElectricityUsage;

        // Calculate costs
        $waterCost = $waterDifference * $utilityPrice->water_price;
        $electricityCost = $electricityDifference * $utilityPrice->electricity_price;

        // Total cost
        $totalCost = $waterCost + $electricityCost + $other;

        // Create invoice
        $invoice = InvoiceDetail::create([
            'user_id' => $userId,
            'room_code' => $roomCode,
            'amount_due' => $totalCost,
            'due_date' => now()->addDays(30), // Set due date to 30 days from now
            'paid' => false,
        ]);

        // Return the output data
        return response()->json([
            'room_floor' => $room->floor,
            'date' => now(),
            'old_usage' => [
                'water' => $oldWaterUsage,
                'electricity' => $oldElectricityUsage,
            ],
            'new_usage' => [
                'water' => $newWaterUsage,
                'electricity' => $newElectricityUsage,
            ],
            'costs' => [
                'water_cost' => $waterCost,
                'electricity_cost' => $electricityCost,
                'other_cost' => $other,
            ],
            'total' => $totalCost,
            'invoice_id' => $invoice->id,
        ]);
    }
}
