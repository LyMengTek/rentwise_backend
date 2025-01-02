<?php

namespace App\Http\Controllers;

use App\Models\InvoiceDetail;
use App\Models\RoomDetail;
use App\Models\UtilityPrice;
use App\Models\UtilityUsage;
use App\Models\RentalDetail;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'rental_id' => 'required|integer|exists:rental_details,id', // Validate rental_id
            'new_water_usage' => 'required|numeric',
            'new_electricity_usage' => 'required|numeric',
            'other' => 'required|numeric',
        ]);

        $rentalId = $request->input('rental_id');
        $newWaterUsage = $request->input('new_water_usage');
        $newElectricityUsage = $request->input('new_electricity_usage');
        $other = $request->input('other');

        // Find the rental details
        $rental = RentalDetail::find($rentalId);
        if (!$rental) {
            return response()->json(['error' => 'Rental not found.'], 404);
        }

        // Find the room details
        $room = RoomDetail::find($rental->room_id);
        if (!$room) {
            return response()->json(['error' => 'Room not found.'], 404);
        }

        // Find the current utility usage for the given rental_id
        $currentUsage = UtilityUsage::where('rental_id', $rentalId)
            ->latest('created_at')
            ->first();

        if (!$currentUsage) {
            return response()->json(['error' => 'Utility usage not found for the specified rental ID.'], 404);
        }

        // Retrieve utility prices using utility_price_id
        $utilityPrice = UtilityPrice::find($room->utility_price_id);
        if (!$utilityPrice) {
            return response()->json(['error' => 'Utility price details not found.'], 404);
        }

        // Calculate old usage
        $oldWaterUsage = $currentUsage->water_usage;
        $oldElectricityUsage = $currentUsage->electricity_usage;

        // Validate that the new usage is not less than the old usage
        if ($newWaterUsage < $oldWaterUsage || $newElectricityUsage < $oldElectricityUsage) {
            return response()->json(['error' => 'New usage cannot be less than previous usage.'], 400);
        }

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
            'rental_id' => $rentalId,
            'amount_due' => $totalCost,
            'due_date' => now()->addDays(30), // Set due date to 30 days from now
            'paid' => false,
        ]);

        $currentUsage->update([
            'water_usage' => $newWaterUsage,
            'electricity_usage' => $newElectricityUsage,
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