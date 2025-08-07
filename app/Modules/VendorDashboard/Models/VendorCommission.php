<?php

namespace App\Modules\VendorDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class VendorCommission extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'commission_rate',
        'min_commission',
        'max_commission',
        'commission_type',
        'tiered_rates',
        'is_active',
        'valid_from',
        'valid_until',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'commission_rate' => 'decimal:2',
        'min_commission' => 'decimal:2',
        'max_commission' => 'decimal:2',
        'tiered_rates' => 'array',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    /**
     * Get the vendor that owns the commission.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Scope a query to only include active commissions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid commissions.
     */
    public function scopeValid($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', $now);
        });
    }

    /**
     * Calculate commission for a given amount.
     */
    public function calculateCommission(float $amount): float
    {
        switch ($this->commission_type) {
            case 'fixed':
                return min($this->commission_rate, $amount);
                
            case 'percentage':
                $commission = $amount * ($this->commission_rate / 100);
                break;
                
            case 'tiered':
                $commission = $this->calculateTieredCommission($amount);
                break;
                
            default:
                $commission = 0;
        }

        // Apply min/max limits
        if ($this->min_commission && $commission < $this->min_commission) {
            $commission = $this->min_commission;
        }
        
        if ($this->max_commission && $commission > $this->max_commission) {
            $commission = $this->max_commission;
        }

        return round($commission, 2);
    }

    /**
     * Calculate tiered commission.
     */
    private function calculateTieredCommission(float $amount): float
    {
        if (!$this->tiered_rates || !is_array($this->tiered_rates)) {
            return 0;
        }

        $commission = 0;
        $remainingAmount = $amount;

        foreach ($this->tiered_rates as $tier) {
            $tierLimit = $tier['limit'] ?? PHP_FLOAT_MAX;
            $tierRate = $tier['rate'] ?? 0;
            
            if ($remainingAmount <= 0) {
                break;
            }

            $tierAmount = min($remainingAmount, $tierLimit);
            $commission += $tierAmount * ($tierRate / 100);
            $remainingAmount -= $tierAmount;
        }

        return $commission;
    }

    /**
     * Check if commission is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        
        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }
        
        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        return true;
    }
}
