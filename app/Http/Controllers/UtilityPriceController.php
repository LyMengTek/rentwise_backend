<?php

namespace App\Http\Controllers;

use App\Models\UtilityPrice;
use Illuminate\Http\Request;

class UtilityPriceController extends Controller
{
    // Function to create a new utility price entry or update an existing one
    public function storeUtilityPrices(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'landlord_id' => 'required|integer', // Ensure landlord_id is an integer
            'water_price' => 'required|numeric|min:0', // Ensure water_price is a valid number
            'electricity_price' => 'required|numeric|min:0', // Ensure electricity_price is a valid number
        ]);
    
        // Use updateOrCreate to either update the existing record or create a new one
        $utilityPrice = UtilityPrice::updateOrCreate(
            ['landlord_id' => $validated['landlord_id']], // Check for an existing landlord_id
            [
                'water_price' => $validated['water_price'], // Set water_price
                'electricity_price' => $validated['electricity_price'], // Set electricity_price
            ]
        );
    
        // Return a JSON response with a success message and the ID of the updated/created record
        return response()->json([
            'message' => 'Utility prices saved successfully',
            'id' => $utilityPrice->id,
        ]);
    }
}
