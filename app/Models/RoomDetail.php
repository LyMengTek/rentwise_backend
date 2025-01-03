<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor',
        'user_id',
        'room_number',
        'room_code',
        'available',
        'description',
        'utility_price_id', // Foreign key to link to UtilityPrice
    ];

    protected $casts = [
        'floor' => 'integer',
        'available' => 'boolean',
        'room_code' => 'integer',
        'description' => 'string',
    ];

    // Relationship with RentalDetail
    public function rental()
    {
        return $this->hasOne(RentalDetail::class, 'room_id');
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

    // Accessor to get formatted prices using the utility price relationship
    public function getFormattedPricesAttribute()
    {
        return $this->belongsTo(UtilityPrice::class, 'utility_price');
    }

    public function invoices()
    {
        return $this->hasMany(InvoiceDetail::class, 'room_code', 'room_code');
    }

    // Updated roomType relationship (Ensure the foreign key matches your table structure)
    public function roomType()
    {
        return $this->belongsTo(RoomTypePrice::class, 'room_type_id');
    }

    public function roomTypePrice()
    {
        return $this->belongsTo(RoomTypePrice::class, 'room_type_price_id');
    }

    public function utilityPrice()
    {
        return $this->belongsTo(UtilityPrice::class, 'utility_price_id');
    }
}
