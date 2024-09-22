<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreviousUtilityUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'water_usage',
        'electricity_usage',
        'other',
    ];

    protected $casts = [
        'month' => 'datetime',
        'year' => 'integer',
        'water_usage' => 'decimal:2',
        'electricity_usage' => 'decimal:2',
        'other' => 'decimal:2',
    ];

    // Relationship with InvoiceDetail
    public function invoice()
    {
        return $this->hasOne(InvoiceDetail::class, 'previous_usage_id');
    }

    // Accessor to get total usage
    public function getTotalUsageAttribute()
    {
        return $this->water_usage + $this->electricity_usage + $this->other;
    }

    // Scope to query by year
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    // Scope to query by month and year
    public function scopeForMonthAndYear($query, $month, $year)
    {
        return $query->whereMonth('month', $month)->where('year', $year);
    }
}
