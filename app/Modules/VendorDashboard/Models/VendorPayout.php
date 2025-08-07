<?php

namespace App\Modules\VendorDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class VendorPayout extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'payout_number',
        'amount',
        'status',
        'payment_method',
        'bank_details',
        'transaction_id',
        'requested_at',
        'processed_at',
        'completed_at',
        'notes',
        'failure_reason',
        'earnings_ids',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'bank_details' => 'array',
        'earnings_ids' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payout) {
            if (empty($payout->payout_number)) {
                $payout->payout_number = static::generatePayoutNumber();
            }
            
            if (empty($payout->requested_at)) {
                $payout->requested_at = now();
            }
        });
    }

    /**
     * Generate unique payout number.
     */
    public static function generatePayoutNumber(): string
    {
        $prefix = 'PO';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        
        do {
            $number = $prefix . $date . $random;
            $exists = static::where('payout_number', $number)->exists();
            if ($exists) {
                $random = strtoupper(substr(uniqid(), -4));
            }
        } while ($exists);
        
        return $number;
    }

    /**
     * Get the vendor that owns the payout.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get related earnings.
     */
    public function getEarningsAttribute()
    {
        if (empty($this->earnings_ids)) {
            return collect();
        }
        
        return VendorEarning::whereIn('id', $this->earnings_ids)->get();
    }

    /**
     * Scope a query to only include pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing payouts.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed payouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed payouts.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Start processing payout.
     */
    public function startProcessing(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
        ]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['payout_number' => $this->payout_number])
            ->log('Ödeme işleme alındı');

        return true;
    }

    /**
     * Mark payout as completed.
     */
    public function markAsCompleted(string $transactionId = null): bool
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'transaction_id' => $transactionId,
        ]);
        
        // Mark related earnings as paid
        if (!empty($this->earnings_ids)) {
            VendorEarning::whereIn('id', $this->earnings_ids)
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'transaction_id' => $this->payout_number,
                ]);
        }
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'payout_number' => $this->payout_number,
                'transaction_id' => $transactionId,
            ])
            ->log('Ödeme tamamlandı');

        return true;
    }

    /**
     * Mark payout as failed.
     */
    public function markAsFailed(string $reason): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'payout_number' => $this->payout_number,
                'reason' => $reason,
            ])
            ->log('Ödeme başarısız oldu');

        return true;
    }

    /**
     * Cancel payout.
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }

        $this->update([
            'status' => 'cancelled',
            'notes' => $reason ? $this->notes . "\nİptal nedeni: " . $reason : $this->notes,
        ]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'payout_number' => $this->payout_number,
                'reason' => $reason,
            ])
            ->log('Ödeme iptal edildi');

        return true;
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Beklemede',
            'processing' => 'İşleniyor',
            'completed' => 'Tamamlandı',
            'failed' => 'Başarısız',
            'cancelled' => 'İptal Edildi',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'failed' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get payment method label.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'bank_transfer' => 'Banka Transferi',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'manual' => 'Manuel',
            default => 'Bilinmiyor',
        };
    }
}
