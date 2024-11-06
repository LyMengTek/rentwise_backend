<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomTypePrice extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'type_price'];

    // Define the inverse of the relationship
    public function roomDetails()
    {
        return $this->hasMany(RoomDetail::class, 'room_type');
    }
}
