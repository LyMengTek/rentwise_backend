<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserDetail;
use App\Models\RoomDetail;
use App\Models\UtilityUsage;
use App\Models\InvoiceDetail;
use App\Models\RentalDetail;
use App\Models\RoomTypePrice;
use Illuminate\Support\Facades\Log;

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

            // Create UtilityUsages for each rental
            $utilityUsage = UtilityUsage::factory()->create([
                'rental_id' => $rental->id,
                'room_code' => $rental->room->room_code,
            ]);
            Log::info("UtilityUsage created: ID {$utilityUsage->id}");

            // Create InvoiceDetails for each rental
            InvoiceDetail::factory()->count(2)->create([
                'rental_id' => $rental->id,
                'room_code' => $utilityUsage->room_code,
                'landlord_id' => $rental->landlord_id,
                'renter_id' => $rental->renter_id,
            ]);
        }
        Log::info('InvoiceDetails created.');

        Log::info('Seeding process completed.');
    }
}