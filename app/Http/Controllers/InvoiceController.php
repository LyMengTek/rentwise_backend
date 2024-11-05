<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'user_id' => 'required|integer|exists:user_details,id',// Validate user_id
            'year' => 'required|integer',
            'month' => 'required|integer',
            'new_water_usage' => 'required|numeric',
            'new_electricity_usage' => 'required|numeric',
            'other' => 'required|numeric',
            'room_code' => 'required|integer',
        ]);

        $userId = $request->input('user_id'); // Get user_id from request
        $year = $request->input('year');
        $month = $request->input('month');
        $newWaterUsage = $request->input('new_water_usage');
        $newElectricityUsage = $request->input('new_electricity_usage');
        $other = $request->input('other');
        $roomCode = $request->input('room_code');

        // Find the current utility usage for the given room code
        $currentUsage = UtilityUsage::where('room_code', $roomCode)
            ->where('year', $year)
            ->whereMonth('month', $month)
            ->first();

        // Check if current usage exists
        if (!$currentUsage) {
            return response()->json(['error' => 'Utility usage not found for the specified room code and date.'], 404);
        }

        // Find room details to get prices
        $room = RoomDetail::where('room_code', $roomCode)->first();
        
        // Check if room exists
        if (!$room) {
            return response()->json(['error' => 'Room not found.'], 404);
        }

        // Calculate old usage
        $oldWaterUsage = $currentUsage->water_usage;
        $oldElectricityUsage = $currentUsage->electricity_usage;

        // Calculate usage differences
        $waterDifference = $newWaterUsage - $oldWaterUsage;
        $electricityDifference = $newElectricityUsage - $oldElectricityUsage;

        // Calculate costs
        $waterCost = $waterDifference * $room->water_price;
        $electricityCost = $electricityDifference * $room->electricity_price;

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
