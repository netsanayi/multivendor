<?php

namespace App\Modules\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Modules\Products\Models\Product;

class Ticket extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ticket_number',
        'category_id',
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'priority',
        'status',
        'user_type',
        'related_order_id',
        'related_product_id',
        'last_activity_at',
        'closed_at',
        'response_count',
        'satisfaction_rating',
        'satisfaction_comment',
        'tags',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_activity_at' => 'datetime',
        'closed_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
        'satisfaction_rating' => 'decimal:1',
        'response_count' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = static::generateTicketNumber();
            }
            
            $ticket->last_activity_at = now();
        });

        static::updating(function ($ticket) {
            // Status değişikliği kontrolü
            if ($ticket->isDirty('status')) {
                if (in_array($ticket->status, ['closed', 'resolved'])) {
                    $ticket->closed_at = now();
                } else {
                    $ticket->closed_at = null;
                }
            }
        });
    }

    /**
     * Generate unique ticket number.
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        do {
            $number = $prefix . $date . $random;
            $exists = static::where('ticket_number', $number)->exists();
            if ($exists) {
                $random = strtoupper(substr(uniqid(), -6));
            }
        } while ($exists);
        
        return $number;
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /**
     * Get the user who created the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the assigned admin/support agent.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the related product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }

    /**
     * Get the responses.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class);
    }

    /**
     * Get the attachments.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    /**
     * Get public responses (not internal).
     */
    public function publicResponses(): HasMany
    {
        return $this->responses()->where('is_internal', false);
    }

    /**
     * Get internal notes.
     */
    public function internalNotes(): HasMany
    {
        return $this->responses()->where('is_internal', true);
    }

    /**
     * Scope open tickets.
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'pending', 'answered', 'on_hold']);
    }

    /**
     * Scope closed tickets.
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['closed', 'resolved']);
    }

    /**
     * Scope by priority.
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope urgent tickets.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope unassigned tickets.
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    /**
     * Scope by user type.
     */
    public function scopeUserType($query, $type)
    {
        return $query->where('user_type', $type);
    }

    /**
     * Check if ticket is open.
     */
    public function isOpen(): bool
    {
        return !in_array($this->status, ['closed', 'resolved']);
    }

    /**
     * Check if ticket is closed.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'resolved']);
    }

    /**
     * Check if user can view the ticket.
     */
    public function canBeViewedBy(User $user): bool
    {
        // Admin her ticket'ı görebilir
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Ticket sahibi görebilir
        if ($this->user_id === $user->id) {
            return true;
        }
        
        // Atanmış kişi görebilir
        if ($this->assigned_to === $user->id) {
            return true;
        }
        
        // Vendor ise ve kendi ürünüyle ilgiliyse görebilir
        if ($user->hasRole('vendor') && $this->related_product_id) {
            // VendorProduct tablosunda kontrol et
            $vendorProduct = \App\Modules\VendorProducts\Models\VendorProduct::where('vendor_id', $user->id)
                ->where('product_id', $this->related_product_id)
                ->exists();
            
            if ($vendorProduct) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Assign ticket to a user.
     */
    public function assignTo(User $user): void
    {
        $this->update(['assigned_to' => $user->id]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['assigned_to' => $user->name])
            ->log('Ticket atandı');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(string $status): void
    {
        $oldStatus = $this->status;
        $this->update(['status' => $status]);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_status' => $oldStatus,
                'new_status' => $status
            ])
            ->log('Ticket durumu güncellendi');
    }

    /**
     * Add response to ticket.
     */
    public function addResponse(string $message, User $user, bool $isInternal = false): TicketResponse
    {
        $response = $this->responses()->create([
            'user_id' => $user->id,
            'message' => $message,
            'is_internal' => $isInternal,
        ]);
        
        // Update ticket
        $this->increment('response_count');
        $this->update([
            'last_activity_at' => now(),
            'status' => $user->hasRole('admin') ? 'answered' : 'pending',
        ]);
        
        return $response;
    }

    /**
     * Rate ticket satisfaction.
     */
    public function rateSatisfaction(float $rating, string $comment = null): void
    {
        $this->update([
            'satisfaction_rating' => $rating,
            'satisfaction_comment' => $comment,
        ]);
    }

    /**
     * Get priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Düşük',
            'normal' => 'Normal',
            'high' => 'Yüksek',
            'urgent' => 'Acil',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Get priority color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'secondary',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open' => 'Açık',
            'pending' => 'Beklemede',
            'answered' => 'Cevaplandı',
            'on_hold' => 'Askıda',
            'closed' => 'Kapalı',
            'resolved' => 'Çözüldü',
            default => 'Bilinmiyor',
        };
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => 'success',
            'pending' => 'warning',
            'answered' => 'info',
            'on_hold' => 'secondary',
            'closed' => 'dark',
            'resolved' => 'primary',
            default => 'secondary',
        };
    }
}
