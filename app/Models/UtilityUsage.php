<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityUsage extends Model
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
        return $this->hasOne(InvoiceDetail::class, 'current_usage_id');
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

    // Method to get usage difference from previous month
    public function getUsageDifferenceFromPrevious()
    {
        $previousMonth = $this->month->copy()->subMonth();
        $previousUsage = self::forMonthAndYear($previousMonth->month, $previousMonth->year)->first();

        if (!$previousUsage) {
            return null;
        }

        return [
            'water' => $this->water_usage - $previousUsage->water_usage,
            'electricity' => $this->electricity_usage - $previousUsage->electricity_usage,
            'other' => $this->other - $previousUsage->other,
        ];
    }

    // Method to get formatted usage
    public function getFormattedUsage()
    {
        return [
            'water' => number_format($this->water_usage, 2) . ' m³',
            'electricity' => number_format($this->electricity_usage, 2) . ' kWh',
            'other' => '$' . number_format($this->other, 2),
        ];
    }
}
