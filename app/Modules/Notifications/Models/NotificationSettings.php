<?php

namespace App\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Users\Models\User;

class NotificationSettings extends Model
{
    protected $table = 'notification_settings';

    protected $fillable = [
        'user_id',
        // Email settings
        'email_enabled',
        'email_order_updates',
        'email_product_updates',
        'email_messages',
        'email_tickets',
        'email_promotions',
        'email_wishlist_updates',
        'email_price_alerts',
        
        // SMS settings
        'sms_enabled',
        'sms_phone',
        'sms_verified',
        'sms_order_updates',
        'sms_important_only',
        
        // Push settings
        'push_enabled',
        'push_order_updates',
        'push_messages',
        'push_promotions',
        
        // Quiet hours
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'timezone',
        
        // Digest
        'digest_frequency',
        'digest_time',
        'digest_day',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'email_order_updates' => 'boolean',
        'email_product_updates' => 'boolean',
        'email_messages' => 'boolean',
        'email_tickets' => 'boolean',
        'email_promotions' => 'boolean',
        'email_wishlist_updates' => 'boolean',
        'email_price_alerts' => 'boolean',
        'sms_enabled' => 'boolean',
        'sms_verified' => 'boolean',
        'sms_order_updates' => 'boolean',
        'sms_important_only' => 'boolean',
        'push_enabled' => 'boolean',
        'push_order_updates' => 'boolean',
        'push_messages' => 'boolean',
        'push_promotions' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a specific channel is enabled.
     */
    public function isChannelEnabled(string $channel): bool
    {
        return match($channel) {
            'email' => $this->email_enabled,
            'sms' => $this->sms_enabled && $this->sms_phone && $this->sms_verified,
            'push' => $this->push_enabled,
            default => false,
        };
    }

    /**
     * Check if a specific notification type is enabled for a channel.
     */
    public function isNotificationEnabled(string $channel, string $type): bool
    {
        if (!$this->isChannelEnabled($channel)) {
            return false;
        }

        $field = $channel . '_' . $type;
        
        return $this->$field ?? false;
    }
}
