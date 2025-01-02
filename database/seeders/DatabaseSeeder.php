<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\UserDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use App\Models\InvoiceDetail;
use App\Models\LandlordFloorRoom;
use App\Models\RentalDetail;
use App\Models\RoomTypePrice;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info('Starting seeding process...');

        // Create UserDetails (10 users: mix of renters and landlords)
        Log::info('Creating UserDetails...');
        $users = UserDetail::factory()->count(10)->create();
        Log::info('UserDetails created.');

        // Create RoomTypePrices (5 room type prices)
        Log::info('Creating RoomTypePrices...');
        $roomTypePrices = RoomTypePrice::factory()->count(5)->create();
        foreach ($roomTypePrices as $index => $roomTypePrice) {
            Log::info("RoomTypePrice {$index} created: ID {$roomTypePrice->id}");
        }
        Log::info('RoomTypePrices created.');

        // Create RoomDetails (5 rooms)
        Log::info('Creating RoomDetails...');
        $rooms = RoomDetail::factory()->count(5)->create();
        foreach ($rooms as $index => $room) {
            Log::info("RoomDetail {$index} created: ID {$room->id}");
        }
        Log::info('RoomDetails created.');

        // Create RentalDetails (10, not all rooms are rented)
        Log::info('Creating RentalDetails...');
        $rentals = RentalDetail::factory()->count(10)->create();
        foreach ($rentals as $index => $rental) {
            Log::info("RentalDetail {$index} created: ID {$rental->id}");
        }
        Log::info('RentalDetails created.');

        // Create InvoiceDetails (20 invoices, allowing for some rooms to have multiple invoices)
        Log::info('Creating InvoiceDetails...');
        $invoices = InvoiceDetail::factory()->count(20)->create();
        foreach ($invoices as $index => $invoice) {
            Log::info("InvoiceDetail {$index} created: ID {$invoice->id}");
        }
        Log::info('InvoiceDetails created.');

        // Create UtilityUsages (5 utility usages)
        Log::info('Creating UtilityUsages...');
        $utilityUsages = UtilityUsage::factory()->count(5)->create();
        foreach ($utilityUsages as $index => $utilityUsage) {
            Log::info("UtilityUsage {$index} created: ID {$utilityUsage->id}");
        }
        Log::info('UtilityUsages created.');

        Log::info('Creating LandlordFloorRooms...');
        LandlordFloorRoom::factory()->count(5)->create();
        Log::info('LandlordFloorRooms created.');

        Log::info('Seeding process completed.');
    }
}