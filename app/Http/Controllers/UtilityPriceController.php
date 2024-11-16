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
            'water_price' => 'required|numeric|min:0',
            'electricity_price' => 'required|numeric|min:0',
        ]);

        // Create a new UtilityPrice entry
        $utilityPrice = UtilityPrice::create([
            'water_price' => $validated['water_price'],
            'electricity_price' => $validated['electricity_price'],
        ]);

        // Return the primary key (ID) of the created entry
        return response()->json([
            'message' => 'Utility prices saved successfully',
            'id' => $utilityPrice->id,
        ]);
    }
}
