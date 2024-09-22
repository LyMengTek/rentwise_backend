<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'floor',
        'landlord_id',
        'utility_id',
        'room_number',
        'available',
        'room_price',
    ];

    protected $casts = [
        'floor' => 'integer',
        'available' => 'boolean',
        'room_price' => 'decimal:2',
    ];

    // Relationship with LandlordDetail
    public function landlord()
    {
        return $this->belongsTo(LandlordDetail::class, 'landlord_id');
    }

    // Relationship with RentalDetail (if you have this model)
    public function rentals()
    {
        return $this->hasMany(RentalDetail::class, 'room_id');
    }

    // Scope to get only available rooms
    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    // Accessor to get full room identifier (e.g., "Floor 2 - Room 201")
    public function getFullRoomIdentifierAttribute()
    {
        return "Floor {$this->floor} - Room {$this->room_number}";
    }
     // You might want to add a method to get formatted price
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->room_price, 2);
    }
}
