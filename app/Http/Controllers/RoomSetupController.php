<?php

namespace App\Http\Controllers;

use App\Models\RoomTypePrice;
use App\Models\LandlordFloorRoom;
use App\Models\UtilityPrice;
use Illuminate\Http\Request;

class RoomSetupController extends Controller
{
    /**
     * Get room, floor, and utility details for the landlord.
     *
     * @param int $landlordId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoomAndFloorDetails($landlordId)
    {
        // Fetch room types and prices
        $roomTypes = RoomTypePrice::where('landlord_id', $landlordId)
            ->get(['type', 'type_price'])
            ->map(function ($item) {
                return [
                    'name' => $this->getRoomTypeName($item->type),
                    'price' => $item->type_price
                ];
            });

        // Fetch floors and rooms
        $floors = LandlordFloorRoom::where('landlord_id', $landlordId)
            ->get()
            ->map(function ($floor) {
                return [
                    'floorNumber' => $floor->floor,
                    'rooms' => explode(',', $floor->rooms)
                ];
            });

        // Fetch utility prices
        $utilityPrices = UtilityPrice::first(); // Assuming there's only one utility price record

        // Prepare the response data
        $response = [
            'isSetupComplete' => $roomTypes->isNotEmpty() && $floors->isNotEmpty(),
            'roomTypes' => $roomTypes,
            'floors' => $floors,
            'utilityPrices' => [
                'waterPrice' => $utilityPrices ? $utilityPrices->water_price : null,
                'electricityPrice' => $utilityPrices ? $utilityPrices->electricity_price : null
            ]
        ];

        return response()->json($response);
    }

    /**
     * Get the room type name based on the type identifier.
     *
     * @param int $type
     * @return string
     */
    private function getRoomTypeName($type)
    {
        switch ($type) {
            case 1:
                return 'Standard';
            case 2:
                return 'Deluxe';
            // Add more cases as needed
            default:
                return 'Unknown';
        }
    }
}
