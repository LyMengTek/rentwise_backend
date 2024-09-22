<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandlordDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'join_code',
    ];

    protected $casts = [
        'join_code' => 'integer',
    ];

    // Relationship with UserDetail
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    // Relationship with RoomDetail
    public function rooms()
    {
        return $this->hasMany(RoomDetail::class, 'landlord_id');
    }

    // Generate a unique join code
    public static function generateJoinCode()
    {
        do {
            $code = mt_rand(100000, 999999);
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    // Get the number of rooms owned by the landlord
    public function getRoomCountAttribute()
    {
        return $this->rooms()->count();
    }

    // Get the number of occupied rooms
    public function getOccupiedRoomCountAttribute()
    {
        return $this->rooms()->where('available', false)->count();
    }

    // Get the occupancy rate
    public function getOccupancyRateAttribute()
    {
        $totalRooms = $this->room_count;
        if ($totalRooms === 0) {
            return 0;
        }
        return ($this->occupied_room_count / $totalRooms) * 100;
    }
}
