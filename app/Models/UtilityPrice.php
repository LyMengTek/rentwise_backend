<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityPrice extends Model
{
    use HasFactory;

    protected $fillable = ['water_price', 'electricity_price'];

    // Define the inverse of the relationship
    public function roomDetails()
    {
        return $this->hasMany(RoomDetail::class, 'utility_price_id');
    }
}
