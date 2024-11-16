<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandlordFloorRoom extends Model
{
    use HasFactory;

     // Define the table name if it's not the plural of the model name
     protected $table = 'landlord_floor_rooms';

     // Specify which attributes are mass assignable
     protected $fillable = [
        'landlord_id',
        'floor',
        'rooms', // Change this to match the column name in your migration
    ];
    

    /**
     * Get the landlord associated with the floor and room summary.
     */
    public function landlord()
    {
        return $this->belongsTo(UserDetail::class, 'landlord_id', 'user_id');
    }
}
