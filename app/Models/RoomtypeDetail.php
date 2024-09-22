<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomtypeDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'room_type',
        'room_price',
    ];

    protected $casts = [
        'room_price' => 'decimal:2',
    ];

    // Relationship with RoomDetail (if you have this model)
    public function rooms()
    {
        return $this->hasMany(RoomDetail::class, 'room_type_id');
    }

    // Relationship with InvoiceDetail
    public function invoices()
    {
        return $this->hasMany(InvoiceDetail::class, 'room_type_id');
    }

    // You might want to add a method to get formatted price
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->room_price, 2);
    }
}
