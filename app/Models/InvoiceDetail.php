<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'usage_id',
        'amount_due',
        'due_date',
        'paid',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'due_date' => 'datetime',
        'paid' => 'boolean',
    ];

    // Relationship with UserDetail
    public function user()
    {
        return $this->belongsTo(UserDetail::class, 'user_id');
    }

    // Relationship with CurrentUtilityUsage
    public function Usage()
    {
        return $this->belongsTo(UtilityUsage::class, 'usage_id');
    }

    // Relationship with RentalDetail
    public function rental()
    {
        return $this->hasOne(RentalDetail::class, 'invoice_id');
    }

    // Scope for unpaid invoices
    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }

    // Scope for overdue invoices
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('paid', false);
    }

    // Method to mark invoice as paid
    public function markAsPaid()
    {
        $this->paid = true;
        $this->save();
    }

    // Accessor to get formatted amount due
    public function getFormattedAmountDueAttribute()
    {
        return '$' . number_format($this->amount_due, 2);
    }

    // Accessor to get usage difference
    public function getUsageDifferenceAttribute()
    {
        if ($this->currentUsage && $this->previousUsage) {
            return [
                'water' => $this->currentUsage->water_usage - $this->previousUsage->water_usage,
                'electricity' => $this->currentUsage->electricity_usage - $this->previousUsage->electricity_usage,
                'other' => $this->currentUsage->other - $this->previousUsage->other,
            ];
        }
        return null;
    }
}
