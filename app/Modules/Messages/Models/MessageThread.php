<?php

namespace App\Modules\Messages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Modules\Products\Models\Product;

class MessageThread extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'subject',
        'product_id',
        'order_id',
        'type',
        'status',
        'last_message_at',
        'message_count',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'last_message_at' => 'datetime',
        'message_count' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            if (empty($thread->last_message_at)) {
                $thread->last_message_at = now();
            }
        });
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the messages.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'thread_id');
    }

    /**
     * Get the participants.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(MessageThreadParticipant::class, 'thread_id');
    }

    /**
     * Get the participant users.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_thread_participants', 'thread_id', 'user_id')
            ->withPivot(['role', 'last_read_at', 'unread_count', 'is_starred', 'is_muted', 'has_left'])
            ->withTimestamps();
    }

    /**
     * Scope active threads.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope threads by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if user is participant.
     */
    public function hasParticipant(User $user): bool
    {
        return $this->participants()
            ->where('user_id', $user->id)
            ->where('has_left', false)
            ->exists();
    }

    /**
     * Add participant to thread.
     */
    public function addParticipant(User $user, string $role = 'customer'): MessageThreadParticipant
    {
        return $this->participants()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'role' => $role,
                'joined_at' => now(),
            ]
        );
    }

    /**
     * Remove participant from thread.
     */
    public function removeParticipant(User $user): void
    {
        $this->participants()
            ->where('user_id', $user->id)
            ->update([
                'has_left' => true,
                'left_at' => now(),
            ]);
    }

    /**
     * Get unread count for user.
     */
    public function getUnreadCountForUser(User $user): int
    {
        $participant = $this->participants()
            ->where('user_id', $user->id)
            ->first();
        
        return $participant ? $participant->unread_count : 0;
    }

    /**
     * Mark as read for user.
     */
    public function markAsReadForUser(User $user): void
    {
        $this->participants()
            ->where('user_id', $user->id)
            ->update([
                'last_read_at' => now(),
                'unread_count' => 0,
            ]);
    }

    /**
     * Increment unread count for all participants except sender.
     */
    public function incrementUnreadCountExcept(User $sender): void
    {
        $this->participants()
            ->where('user_id', '!=', $sender->id)
            ->where('has_left', false)
            ->where('is_muted', false)
            ->increment('unread_count');
    }

    /**
     * Send message to thread.
     */
    public function sendMessage(User $sender, string $message, string $type = 'text', array $data = []): Message
    {
        $messageModel = $this->messages()->create(array_merge([
            'sender_id' => $sender->id,
            'message' => $message,
            'type' => $type,
        ], $data));
        
        // Update thread
        $this->update([
            'last_message_at' => now(),
            'message_count' => $this->messages()->count(),
        ]);
        
        // Update unread counts
        $this->incrementUnreadCountExcept($sender);
        
        return $messageModel;
    }

    /**
     * Get latest message.
     */
    public function getLatestMessageAttribute(): ?Message
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get vendor participant.
     */
    public function getVendorAttribute(): ?User
    {
        $participant = $this->participants()
            ->where('role', 'vendor')
            ->first();
        
        return $participant ? $participant->user : null;
    }

    /**
     * Get customer participant.
     */
    public function getCustomerAttribute(): ?User
    {
        $participant = $this->participants()
            ->where('role', 'customer')
            ->first();
        
        return $participant ? $participant->user : null;
    }

    /**
     * Archive thread.
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Block thread.
     */
    public function block(): void
    {
        $this->update(['status' => 'blocked']);
    }

    /**
     * Reactivate thread.
     */
    public function reactivate(): void
    {
        $this->update(['status' => 'active']);
    }
}
