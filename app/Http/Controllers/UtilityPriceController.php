<?php

namespace App\Http\Controllers;

use App\Models\UtilityPrice;
use Illuminate\Http\Request;

class UtilityPriceController extends Controller
{
    // Function to create a new utility price entry and return the ID
    public function storeUtilityPrices(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'landlord_id' => 'required',
            'water_price' => 'required|numeric|min:0',
            'electricity_price' => 'required|numeric|min:0',
        ]);
    
        // Use updateOrCreate to either update the existing record or create a new one
        $utilityPrice = UtilityPrice::updateOrCreate(
            ['landlord_id' => $validated['landlord_id']], // Check for existing landlord_id
            [
                'water_price' => $validated['water_price'], // Update water_price
                'electricity_price' => $validated['electricity_price'], // Update electricity_price
            ]
        );
    
        // Return the primary key (ID) of the updated or created entry
        return response()->json([
            'message' => 'Utility prices saved successfully',
            'id' => $utilityPrice->id,
        ]);
    }
    
}
