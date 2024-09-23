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
            'room_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create the utility usage record
        $utility = UtilityUsage::create([
            'month' => $request->month,
            'year' => $request->year,
            'water_usage' => $request->water_usage,
            'electricity_usage' => $request->electricity_usage,
            'other' => $request->other ?? 0, // Default to 0 if not provided
            'room_code' => $request->room_code,
        ]);

        // Return the created utility usage as a JSON response
        return response()->json($utility, 201);
    }
}
