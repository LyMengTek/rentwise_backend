<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'username',
        'password',
        'email',
        'phone_number',
        'profile_picture',
        'id_card_picture',
        'user_type',
        'join_code',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'user_type' => 'string',
        'join_code' => 'integer',
    ];


    // Relationship with InvoiceDetail
    public function invoices()
    {
        return $this->hasMany(InvoiceDetail::class, 'user_id');
    }

    // Relationship with RentalDetail (if you have this model)
    public function rentals()
    {
        return $this->hasMany(RentalDetail::class, 'user_id');
    }

    // You might want to add a method to check if the user is a landlord
    public function isLandlord()
    {
        return $this->user_type === 'landlord';
    }

    // You might want to add a method to check if the user is a renter
    public function isRenter()
    {
        return $this->user_type === 'renter';
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


        public function getRentersByJoinCode(Request $request): JsonResponse
        {
            // Get the currently authenticated landlord
            $landlord = UserDetail::find($request->landlord_id);
    
            // Ensure that the user is a landlord
            if (!$landlord || !$landlord->isLandlord()) {
                return response()->json(['error' => 'User is not a landlord or not found'], 403);
            }
    
            // Fetch all renters who share the landlord's join_code
            $renters = UserDetail::where('join_code', $landlord->join_code)
                                ->where('user_type', 'renter')
                                ->get();
    
            // Return the renters as a JSON response
            return response()->json($renters);
        }
}
