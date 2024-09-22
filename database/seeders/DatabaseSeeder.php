<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserDetail;
use App\Models\RoomtypeDetail;
use App\Models\LandlordDetail;
use App\Models\RoomDetail;
use App\Models\CurrentUtilityUsage;
use App\Models\PreviousUtilityUsage;
use App\Models\InvoiceDetail;
use App\Models\RentalDetail;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create UserDetails (10 users: mix of renters and landlords)
        UserDetail::factory()->count(10)->create();

        // Create RoomtypeDetails (3 room types)
        RoomtypeDetail::factory()->count(3)->create();

        // Create LandlordDetails (3 landlords)
        LandlordDetail::factory()->count(3)->create();

        // Create RoomDetails (15 rooms, about 5 per landlord)
        RoomDetail::factory()->count(15)->create();

        // Create CurrentUtilityUsages (15, one for each room)
        CurrentUtilityUsage::factory()->count(15)->create();

        // Create PreviousUtilityUsages (15, one for each room)
        PreviousUtilityUsage::factory()->count(15)->create();

        // Create InvoiceDetails (20, allowing for some rooms to have multiple invoices)
        InvoiceDetail::factory()->count(20)->create();

        // Create RentalDetails (10, not all rooms are rented)
        RentalDetail::factory()->count(10)->create();
    }
}
