<?php

namespace App\Modules\Notifications\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notification as BaseNotification;

class NotificationService
{
    /**
     * Send notification to user(s).
     */
    public function send($users, BaseNotification $notification): void
    {
        Notification::send($users, $notification);
    }
    
    /**
     * Send notification now (not queued).
     */
    public function sendNow($users, BaseNotification $notification): void
    {
        Notification::sendNow($users, $notification);
    }
    
    /**
     * Queue notification for later.
     */
    public function queue(User $user, string $type, array $data, string $channel = 'email', $scheduledAt = null): void
    {
        DB::table('notification_queue')->insert([
            'user_id' => $user->id,
            'type' => $type,
            'channel' => $channel,
            'subject' => $data['subject'] ?? null,
            'content' => $data['content'] ?? '',
            'data' => json_encode($data['data'] ?? []),
            'priority' => $data['priority'] ?? 'normal',
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Process notification queue.
     */
    public function processQueue(): int
    {
        $notifications = DB::table('notification_queue')
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->limit(100)
            ->get();
        
        $processed = 0;
        
        foreach ($notifications as $notification) {
            try {
                // Update status to processing
                DB::table('notification_queue')
                    ->where('id', $notification->id)
                    ->update(['status' => 'processing']);
                
                // Send notification based on channel
                switch ($notification->channel) {
                    case 'email':
                        $this->sendEmail($notification);
                        break;
                    case 'sms':
                        $this->sendSms($notification);
                        break;
                    case 'push':
                        $this->sendPush($notification);
                        break;
                    case 'database':
                        $this->sendDatabase($notification);
                        break;
                }
                
                // Mark as sent
                DB::table('notification_queue')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => 'sent',
                        'sent_at' => now(),
                    ]);
                
                // Add to history
                $this->addToHistory($notification);
                
                $processed++;
            } catch (\Exception $e) {
                // Mark as failed
                DB::table('notification_queue')
                    ->where('id', $notification->id)
                    ->update([
                        'status' => 'failed',
                        'attempts' => DB::raw('attempts + 1'),
                        'error_message' => $e->getMessage(),
                    ]);
            }
        }
        
        return $processed;
    }
    
    /**
     * Send email notification.
     */
    private function sendEmail($notification): void
    {
        $user = User::find($notification->user_id);
        $data = json_decode($notification->data, true);
        
        // Check quiet hours
        if ($this->isInQuietHours($user)) {
            throw new \Exception('Sessiz saatler içinde');
        }
        
        // Send email using Laravel Mail
        Mail::to($user->email)->send(new \App\Mail\GenericNotification(
            $notification->subject,
            $notification->content,
            $data
        ));
    }
    
    /**
     * Send SMS notification.
     */
    private function sendSms($notification): void
    {
        $user = User::find($notification->user_id);
        $settings = $user->notificationSettings;
        
        if (!$settings || !$settings->sms_enabled || !$settings->sms_phone) {
            throw new \Exception('SMS ayarları yapılmamış');
        }
        
        // Check SMS credits
        $credits = DB::table('sms_credits')
            ->where('user_id', $user->id)
            ->first();
        
        if (!$credits || $credits->credits <= 0) {
            throw new \Exception('SMS kredisi yetersiz');
        }
        
        // Send SMS via provider (example with Twilio-like API)
        $response = Http::post(config('services.sms.endpoint'), [
            'to' => $settings->sms_phone,
            'message' => $notification->content,
            'from' => config('services.sms.from'),
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('SMS gönderilemedi: ' . $response->body());
        }
        
        // Deduct credit
        DB::table('sms_credits')
            ->where('user_id', $user->id)
            ->decrement('credits');
        
        DB::table('sms_credits')
            ->where('user_id', $user->id)
            ->increment('used_credits');
    }
    
    /**
     * Send push notification.
     */
    private function sendPush($notification): void
    {
        $user = User::find($notification->user_id);
        $data = json_decode($notification->data, true);
        
        // Get user's push tokens
        $tokens = DB::table('push_tokens')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
        
        if ($tokens->isEmpty()) {
            throw new \Exception('Push token bulunamadı');
        }
        
        foreach ($tokens as $token) {
            try {
                $this->sendPushToToken($token, $notification->content, $data);
                
                // Update last used
                DB::table('push_tokens')
                    ->where('id', $token->id)
                    ->update(['last_used_at' => now()]);
            } catch (\Exception $e) {
                // Deactivate invalid token
                DB::table('push_tokens')
                    ->where('id', $token->id)
                    ->update(['is_active' => false]);
            }
        }
    }
    
    /**
     * Send push to specific token.
     */
    private function sendPushToToken($token, string $message, array $data): void
    {
        switch ($token->platform) {
            case 'web':
                // Web Push API
                $this->sendWebPush($token->token, $message, $data);
                break;
            case 'ios':
                // APNS
                $this->sendApnsPush($token->token, $message, $data);
                break;
            case 'android':
                // FCM
                $this->sendFcmPush($token->token, $message, $data);
                break;
        }
    }
    
    /**
     * Send Web Push notification.
     */
    private function sendWebPush(string $token, string $message, array $data): void
    {
        // Implementation would use Web Push libraries
        // Example: Minishlink\WebPush
    }
    
    /**
     * Send APNS push notification.
     */
    private function sendApnsPush(string $token, string $message, array $data): void
    {
        // Implementation would use APNS
    }
    
    /**
     * Send FCM push notification.
     */
    private function sendFcmPush(string $token, string $message, array $data): void
    {
        $response = Http::withHeaders([
            'Authorization' => 'key=' . config('services.fcm.server_key'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token,
            'notification' => [
                'title' => $data['title'] ?? config('app.name'),
                'body' => $message,
                'icon' => $data['icon'] ?? '/icon.png',
                'click_action' => $data['url'] ?? '/',
            ],
            'data' => $data,
        ]);
        
        if (!$response->successful()) {
            throw new \Exception('FCM push gönderilemedi');
        }
    }
    
    /**
     * Send database notification.
     */
    private function sendDatabase($notification): void
    {
        $user = User::find($notification->user_id);
        $data = json_decode($notification->data, true);
        
        DB::table('notifications')->insert([
            'id' => \Str::uuid(),
            'type' => $notification->type,
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'message' => $notification->content,
                'data' => $data,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Add notification to history.
     */
    private function addToHistory($notification): void
    {
        DB::table('notification_history')->insert([
            'user_id' => $notification->user_id,
            'type' => $notification->type,
            'channel' => $notification->channel,
            'subject' => $notification->subject,
            'content' => $notification->content,
            'data' => $notification->data,
            'sent_at' => now(),
            'is_success' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Check if current time is in quiet hours for user.
     */
    private function isInQuietHours(User $user): bool
    {
        $settings = $user->notificationSettings;
        
        if (!$settings || !$settings->quiet_hours_enabled) {
            return false;
        }
        
        $timezone = $settings->timezone ?? 'Europe/Istanbul';
        $now = now()->setTimezone($timezone);
        $currentTime = $now->format('H:i');
        
        $start = $settings->quiet_hours_start;
        $end = $settings->quiet_hours_end;
        
        if ($start < $end) {
            return $currentTime >= $start && $currentTime <= $end;
        } else {
            // Overnight quiet hours
            return $currentTime >= $start || $currentTime <= $end;
        }
    }
    
    /**
     * Get user's notification statistics.
     */
    public function getUserStatistics(User $user): array
    {
        $history = DB::table('notification_history')
            ->where('user_id', $user->id)
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN is_success = true THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read'),
                DB::raw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked')
            )
            ->first();
        
        $byChannel = DB::table('notification_history')
            ->where('user_id', $user->id)
            ->select('channel', DB::raw('COUNT(*) as count'))
            ->groupBy('channel')
            ->get()
            ->pluck('count', 'channel')
            ->toArray();
        
        $byType = DB::table('notification_history')
            ->where('user_id', $user->id)
            ->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();
        
        return [
            'total' => $history->total ?? 0,
            'successful' => $history->successful ?? 0,
            'read' => $history->read ?? 0,
            'clicked' => $history->clicked ?? 0,
            'read_rate' => $history->total > 0 ? round(($history->read / $history->total) * 100, 2) : 0,
            'click_rate' => $history->total > 0 ? round(($history->clicked / $history->total) * 100, 2) : 0,
            'by_channel' => $byChannel,
            'by_type' => $byType,
        ];
    }
    
    /**
     * Update user's notification settings.
     */
    public function updateSettings(User $user, array $settings): void
    {
        DB::table('notification_settings')->updateOrInsert(
            ['user_id' => $user->id],
            array_merge($settings, [
                'updated_at' => now(),
            ])
        );
    }
    
    /**
     * Register push token.
     */
    public function registerPushToken(User $user, string $token, string $platform, array $deviceInfo = []): void
    {
        DB::table('push_tokens')->updateOrInsert(
            [
                'user_id' => $user->id,
                'token' => $token,
            ],
            [
                'platform' => $platform,
                'device_id' => $deviceInfo['device_id'] ?? null,
                'device_name' => $deviceInfo['device_name'] ?? null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
    
    /**
     * Unregister push token.
     */
    public function unregisterPushToken(string $token): void
    {
        DB::table('push_tokens')
            ->where('token', $token)
            ->update(['is_active' => false]);
    }
    
    /**
     * Send digest notifications.
     */
    public function sendDigests(): int
    {
        $users = User::whereHas('notificationSettings', function ($query) {
            $query->where('digest_frequency', '!=', 'never');
        })->get();
        
        $sent = 0;
        
        foreach ($users as $user) {
            if ($this->shouldSendDigest($user)) {
                $this->sendDigestToUser($user);
                $sent++;
            }
        }
        
        return $sent;
    }
    
    /**
     * Check if digest should be sent to user.
     */
    private function shouldSendDigest(User $user): bool
    {
        $settings = $user->notificationSettings;
        $lastDigest = DB::table('notification_history')
            ->where('user_id', $user->id)
            ->where('type', 'digest')
            ->latest('sent_at')
            ->first();
        
        if (!$lastDigest) {
            return true;
        }
        
        $lastSent = \Carbon\Carbon::parse($lastDigest->sent_at);
        
        switch ($settings->digest_frequency) {
            case 'daily':
                return $lastSent->diffInDays(now()) >= 1;
            case 'weekly':
                return $lastSent->diffInWeeks(now()) >= 1;
            case 'monthly':
                return $lastSent->diffInMonths(now()) >= 1;
            default:
                return false;
        }
    }
    
    /**
     * Send digest to user.
     */
    private function sendDigestToUser(User $user): void
    {
        $settings = $user->notificationSettings;
        
        // Get unread notifications
        $notifications = DB::table('notifications')
            ->where('notifiable_id', $user->id)
            ->whereNull('read_at')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();
        
        if ($notifications->isEmpty()) {
            return;
        }
        
        // Group by type
        $grouped = $notifications->groupBy('type');
        
        // Create digest content
        $content = $this->createDigestContent($grouped);
        
        // Queue digest email
        $this->queue($user, 'digest', [
            'subject' => 'Haftalık Özet - ' . config('app.name'),
            'content' => $content,
            'data' => [
                'notification_count' => $notifications->count(),
                'types' => array_keys($grouped->toArray()),
            ],
        ], 'email');
    }
    
    /**
     * Create digest content.
     */
    private function createDigestContent($groupedNotifications): string
    {
        $content = "İşte bu haftaki aktivitelerinizin özeti:\n\n";
        
        foreach ($groupedNotifications as $type => $notifications) {
            $content .= $this->getTypeLabel($type) . " ({$notifications->count()})\n";
            
            foreach ($notifications->take(5) as $notification) {
                $data = json_decode($notification->data, true);
                $content .= "- " . ($data['message'] ?? 'Bildirim') . "\n";
            }
            
            if ($notifications->count() > 5) {
                $remaining = $notifications->count() - 5;
                $content .= "... ve {$remaining} daha fazla\n";
            }
            
            $content .= "\n";
        }
        
        return $content;
    }
    
    /**
     * Get type label.
     */
    private function getTypeLabel(string $type): string
    {
        return match($type) {
            'product_liked' => '🛍️ Ürün Beğenileri',
            'new_message' => '💬 Yeni Mesajlar',
            'ticket_update' => '🎫 Destek Talepleri',
            'order_update' => '📦 Sipariş Güncellemeleri',
            'price_alert' => '💰 Fiyat Uyarıları',
            default => '📢 Bildirimler',
        };
    }
}
