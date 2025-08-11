<?php

namespace App\Modules\Messages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageThreadParticipant extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'message_thread_participants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'thread_id',
        'user_id',
        'last_read_at',
        'is_starred',
        'is_muted',
        'is_archived',
        'deleted_at',
        'joined_at',
        'left_at',
        'role'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_read_at' => 'datetime',
        'is_starred' => 'boolean',
        'is_muted' => 'boolean',
        'is_archived' => 'boolean',
        'deleted_at' => 'datetime',
        'joined_at' => 'datetime',
        'left_at' => 'datetime'
    ];

    /**
     * Get the thread that this participant belongs to.
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class, 'thread_id');
    }

    /**
     * Get the user that is participating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Users\Models\User::class, 'user_id');
    }

    /**
     * Scope a query to only include active participants.
     */
    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }

    /**
     * Scope a query to only include participants who haven't deleted the thread.
     */
    public function scopeNotDeleted($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Mark all messages as read for this participant.
     */
    public function markAsRead(): void
    {
        $this->update(['last_read_at' => now()]);
    }

    /**
     * Get unread messages count for this participant.
     */
    public function getUnreadCountAttribute(): int
    {
        return $this->thread->messages()
            ->where('user_id', '!=', $this->user_id)
            ->where(function ($query) {
                $query->whereNull('read_at')
                    ->orWhere('read_at', '>', $this->last_read_at ?? '1970-01-01');
            })
            ->count();
    }

    /**
     * Check if participant has unread messages.
     */
    public function hasUnreadMessages(): bool
    {
        return $this->unread_count > 0;
    }

    /**
     * Toggle star status.
     */
    public function toggleStar(): void
    {
        $this->update(['is_starred' => !$this->is_starred]);
    }

    /**
     * Toggle mute status.
     */
    public function toggleMute(): void
    {
        $this->update(['is_muted' => !$this->is_muted]);
    }

    /**
     * Archive the thread for this participant.
     */
    public function archive(): void
    {
        $this->update(['is_archived' => true]);
    }

    /**
     * Unarchive the thread for this participant.
     */
    public function unarchive(): void
    {
        $this->update(['is_archived' => false]);
    }

    /**
     * Leave the thread.
     */
    public function leave(): void
    {
        $this->update(['left_at' => now()]);
    }

    /**
     * Rejoin the thread.
     */
    public function rejoin(): void
    {
        $this->update(['left_at' => null]);
    }

    /**
     * Soft delete the thread for this participant.
     */
    public function softDelete(): void
    {
        $this->update(['deleted_at' => now()]);
    }

    /**
     * Restore the thread for this participant.
     */
    public function restore(): void
    {
        $this->update(['deleted_at' => null]);
    }
}
