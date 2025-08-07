<?php

namespace App\Modules\VendorDashboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class VendorEarning extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'order_id',
        'order_item_id',
        'gross_amount',
        'commission_amount',
        'net_amount',
        'tax_amount',
        'status',
        'payment_method',
        'transaction_id',
        'paid_at',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gross_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the vendor that owns the earning.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Scope a query to only include pending earnings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved earnings.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include paid earnings.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope a query to only include unpaid earnings.
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Mark earning as approved.
     */
    public function approve(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->update(['status' => 'approved']);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['old_status' => 'pending', 'new_status' => 'approved'])
            ->log('Vendor kazancı onaylandı');

        return true;
    }

    /**
     * Mark earning as paid.
     */
    public function markAsPaid(string $transactionId = null, string $paymentMethod = null): bool
    {
        if (!in_array($this->status, ['pending', 'approved'])) {
            return false;
        }

        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
        ]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'transaction_id' => $transactionId,
                'payment_method' => $paymentMethod,
            ])
            ->log('Vendor ödemesi yapıldı');

        return true;
    }

    /**
     * Cancel earning.
     */
    public function cancel(string $reason = null): bool
    {
        if (in_array($this->status, ['paid', 'cancelled'])) {
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
            ->withProperties(['reason' => $reason])
            ->log('Vendor kazancı iptal edildi');

        return true;
    }

    /**
     * Refund earning.
     */
    public function refund(string $reason = null): bool
    {
        if ($this->status !== 'paid') {
            return false;
        }

        $this->update([
            'status' => 'refunded',
            'notes' => $reason ? $this->notes . "\nİade nedeni: " . $reason : $this->notes,
        ]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $reason])
            ->log('Vendor ödemesi iade edildi');

        return true;
    }

    /**
     * Check if earning can be paid.
     */
    public function canBePaid(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Beklemede',
            'approved' => 'Onaylandı',
            'paid' => 'Ödendi',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi',
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
            'approved' => 'blue',
            'paid' => 'green',
            'cancelled' => 'red',
            'refunded' => 'orange',
            default => 'gray',
        };
    }
}
