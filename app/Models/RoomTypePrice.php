<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypePrice extends Model
{
    use HasFactory;

    protected $fillable = ['landlord_id', 'type', 'type_price'];

    /**
     * Define the relationship with UserDetail (landlord).
     */
    public function landlord()
    {
        return $this->belongsTo(UserDetail::class, 'landlord_id');
    }

    /**
     * Define the relationship with RoomDetail.
     */
    public function roomDetails()
    {
        return $this->hasMany(RoomDetail::class, 'room_type');
    }

    public function rentalDetails()
    {
        return $this->hasMany(RentalDetail::class, 'room_type_price_id');
    }
}
