<?php

namespace App\Modules\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class TicketResponse extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'is_solution',
        'attachments',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_internal' => 'boolean',
        'is_solution' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($response) {
            if (empty($response->ip_address)) {
                $response->ip_address = request()->ip();
            }
            
            if (empty($response->user_agent)) {
                $response->user_agent = request()->userAgent();
            }
        });
    }

    /**
     * Get the ticket.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachments.
     */
    public function attachmentFiles(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'response_id');
    }

    /**
     * Scope public responses.
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    /**
     * Scope internal responses.
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope solution responses.
     */
    public function scopeSolution($query)
    {
        return $query->where('is_solution', true);
    }

    /**
     * Mark as solution.
     */
    public function markAsSolution(): void
    {
        $this->update(['is_solution' => true]);
        
        // Update ticket status
        $this->ticket->updateStatus('resolved');
        
        // Log activity
        activity()
            ->performedOn($this->ticket)
            ->causedBy(auth()->user())
            ->log('Ticket çözüm olarak işaretlendi');
    }

    /**
     * Unmark as solution.
     */
    public function unmarkAsSolution(): void
    {
        $this->update(['is_solution' => false]);
        
        // Update ticket status
        $this->ticket->updateStatus('open');
    }

    /**
     * Check if user can view the response.
     */
    public function canBeViewedBy(User $user): bool
    {
        // Internal notları sadece admin görebilir
        if ($this->is_internal && !$user->hasRole('admin')) {
            return false;
        }
        
        // Ticket'ı görebiliyorsa response'u da görebilir
        return $this->ticket->canBeViewedBy($user);
    }

    /**
     * Get formatted message (with mentions, links, etc.).
     */
    public function getFormattedMessageAttribute(): string
    {
        $message = e($this->message);
        
        // Convert URLs to links
        $message = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener">$1</a>',
            $message
        );
        
        // Convert mentions (@username)
        $message = preg_replace(
            '/@([a-zA-Z0-9_]+)/',
            '<span class="mention">@$1</span>',
            $message
        );
        
        // Convert newlines to <br>
        $message = nl2br($message);
        
        return $message;
    }
}
