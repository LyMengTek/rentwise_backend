<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentalDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'landlord_id',
        'renter_id',
        'room_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(RoomDetail::class, 'room_id');
    }

    public function utilityUsage()
    {
        return $this->hasOne(UtilityUsage::class, 'room_code', 'room_code');
    }

    public function roomTypePrice()
    {
        return $this->belongsTo(RoomTypePrice::class, 'room_type_price_id');
    }

    public function invoices()
    {
        return $this->hasMany(InvoiceDetail::class, 'rental_id');
    }

    public function landlord()
    {
        return $this->belongsTo(UserDetail::class, 'landlord_id');
    }

    public function renter()
    {
        return $this->belongsTo(UserDetail::class, 'renter_id');
    }

    // Relationship with UserDetail
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    // Scope to get only active rentals
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor to get rental duration in days
    public function getRentalDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date);
    }

    // Method to check if the rental is currently active
    public function isCurrentlyActive()
    {
        $now = now();
        return $this->is_active && $this->start_date <= $now && $this->end_date >= $now;
    }

}
