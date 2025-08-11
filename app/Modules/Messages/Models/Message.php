<?php

namespace App\Modules\Messages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'thread_id',
        'sender_id',
        'message',
        'type',
        'attachments',
        'offer_amount',
        'offer_status',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
        'read_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'attachments' => 'array',
        'offer_amount' => 'decimal:2',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'read_by' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the thread.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class, 'thread_id');
    }

    /**
     * Get the sender.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the attachments.
     */
    public function attachmentFiles(): HasMany
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    /**
     * Scope text messages.
     */
    public function scopeText($query)
    {
        return $query->where('type', 'text');
    }

    /**
     * Scope offer messages.
     */
    public function scopeOffers($query)
    {
        return $query->where('type', 'offer');
    }

    /**
     * Scope active messages (not deleted).
     */
    public function scopeActive($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Mark as read by user.
     */
    public function markAsReadBy(User $user): void
    {
        $readBy = $this->read_by ?? [];
        
        if (!in_array($user->id, $readBy)) {
            $readBy[] = $user->id;
            $this->update(['read_by' => $readBy]);
        }
    }

    /**
     * Check if read by user.
     */
    public function isReadBy(User $user): bool
    {
        $readBy = $this->read_by ?? [];
        return in_array($user->id, $readBy);
    }

    /**
     * Edit message.
     */
    public function edit(string $newMessage): void
    {
        $this->update([
            'message' => $newMessage,
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    /**
     * Soft delete message.
     */
    public function softDelete(): void
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);
    }

    /**
     * Accept offer.
     */
    public function acceptOffer(): void
    {
        if ($this->type !== 'offer') {
            throw new \Exception('Bu mesaj bir teklif değil.');
        }
        
        $this->update(['offer_status' => 'accepted']);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['offer_amount' => $this->offer_amount])
            ->log('Teklif kabul edildi');
    }

    /**
     * Reject offer.
     */
    public function rejectOffer(): void
    {
        if ($this->type !== 'offer') {
            throw new \Exception('Bu mesaj bir teklif değil.');
        }
        
        $this->update(['offer_status' => 'rejected']);
        
        // Log activity
        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties(['offer_amount' => $this->offer_amount])
            ->log('Teklif reddedildi');
    }

    /**
     * Get formatted message.
     */
    public function getFormattedMessageAttribute(): string
    {
        if ($this->is_deleted) {
            return '<em class="text-muted">Bu mesaj silinmiştir.</em>';
        }
        
        $message = e($this->message);
        
        // Convert URLs to links
        $message = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener">$1</a>',
            $message
        );
        
        // Convert newlines to <br>
        $message = nl2br($message);
        
        if ($this->is_edited) {
            $message .= ' <small class="text-muted">(düzenlendi)</small>';
        }
        
        return $message;
    }

    /**
     * Get display message based on type.
     */
    public function getDisplayMessageAttribute(): string
    {
        switch ($this->type) {
            case 'offer':
                return "₺{$this->offer_amount} tutarında teklif gönderildi";
            case 'image':
                $count = count($this->attachments ?? []);
                return "{$count} resim gönderildi";
            case 'file':
                $count = count($this->attachments ?? []);
                return "{$count} dosya gönderildi";
            case 'system':
                return $this->message;
            default:
                return $this->message;
        }
    }
}
