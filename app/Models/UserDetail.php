<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'user_type' => 'string',
    ];

    // Relationship with LandlordDetail
    public function landlordDetail()
    {
        return $this->hasOne(LandlordDetail::class, 'user_id');
    }

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
}
