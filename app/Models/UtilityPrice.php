<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id', // Ensure this field is set
        'water_price',
        'electricity_price',
    ];

    // Define the inverse of the relationship
    public function roomDetails()
    {
        return $this->hasMany(RoomDetail::class, 'utility_price_id');
    }
}
