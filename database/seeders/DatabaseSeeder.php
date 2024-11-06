<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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
        // Create UserDetails (10 users: mix of renters and landlords)
        UserDetail::factory()->count(10)->create();

        // Create UtilityUsages and RoomDetails (15 rooms, one utility usage per room)
        $utilityUsages = UtilityUsage::factory()->count(15)->create();

        foreach ($utilityUsages as $utilityUsage) {
            RoomDetail::factory()->create([
                'room_code' => $utilityUsage->room_code,
            ]);
        }

        // Create InvoiceDetails (20, allowing for some rooms to have multiple invoices)
        foreach ($utilityUsages as $utilityUsage) {
            InvoiceDetail::factory()->count(2)->create([
                'room_code' => $utilityUsage->room_code,
            ]);
        }

        // Create RentalDetails (10, not all rooms are rented)
        RentalDetail::factory()->count(10)->create();
        LandlordFloorRoom::factory()->count(5)->create();
        RoomTypePrice::factory()->count(10)->create();
    }
}
