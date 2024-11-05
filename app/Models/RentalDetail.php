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
        'invoice_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationship with UserDetail
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    // Relationship with RoomDetail
    public function room()
    {
        return $this->belongsTo(RoomDetail::class, 'room_id');
    }

    // Relationship with InvoiceDetail
    public function invoice()
    {
        return $this->belongsTo(InvoiceDetail::class, 'invoice_id');
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
