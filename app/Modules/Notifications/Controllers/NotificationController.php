<?php

namespace App\Modules\Notifications\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        $this->middleware('auth');
    }

    /**
     * Display user's notifications.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Database notifications
        $notifications = $user->notifications()
            ->when($request->unread, function ($query) {
                return $query->unread();
            })
            ->latest()
            ->paginate(20);
        
        // Statistics
        $stats = $this->notificationService->getUserStatistics($user);
        
        return view('notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Display notification settings.
     */
    public function settings()
    {
        $user = auth()->user();
        
        $settings = DB::table('notification_settings')
            ->where('user_id', $user->id)
            ->first();
        
        // Create default settings if not exists
        if (!$settings) {
            DB::table('notification_settings')->insert([
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $settings = DB::table('notification_settings')
                ->where('user_id', $user->id)
                ->first();
        }
        
        return view('notifications.settings', compact('settings'));
    }

    /**
     * Update notification settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            // Email settings
            'email_enabled' => 'nullable|boolean',
            'email_order_updates' => 'nullable|boolean',
            'email_product_updates' => 'nullable|boolean',
            'email_messages' => 'nullable|boolean',
            'email_tickets' => 'nullable|boolean',
            'email_promotions' => 'nullable|boolean',
            'email_wishlist_updates' => 'nullable|boolean',
            'email_price_alerts' => 'nullable|boolean',
            
            // SMS settings
            'sms_enabled' => 'nullable|boolean',
            'sms_phone' => 'nullable|required_if:sms_enabled,true|string',
            'sms_order_updates' => 'nullable|boolean',
            'sms_important_only' => 'nullable|boolean',
            
            // Push settings
            'push_enabled' => 'nullable|boolean',
            'push_order_updates' => 'nullable|boolean',
            'push_messages' => 'nullable|boolean',
            'push_promotions' => 'nullable|boolean',
            
            // Quiet hours
            'quiet_hours_enabled' => 'nullable|boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
            'timezone' => 'nullable|timezone',
            
            // Digest
            'digest_frequency' => 'nullable|in:never,daily,weekly,monthly',
            'digest_time' => 'nullable|date_format:H:i',
            'digest_day' => 'nullable|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);
        
        // Convert checkboxes to boolean
        foreach (['email_enabled', 'email_order_updates', 'email_product_updates', 'email_messages', 
                 'email_tickets', 'email_promotions', 'email_wishlist_updates', 'email_price_alerts',
                 'sms_enabled', 'sms_order_updates', 'sms_important_only',
                 'push_enabled', 'push_order_updates', 'push_messages', 'push_promotions',
                 'quiet_hours_enabled'] as $field) {
            $validated[$field] = $request->has($field) ? 1 : 0;
        }
        
        $this->notificationService->updateSettings(auth()->user(), $validated);
        
        return redirect()
            ->route('notifications.settings')
            ->with('success', 'Bildirim ayarlarınız güncellendi.');
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }

    /**
     * Delete notification.
     */
    public function destroy(Request $request, $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }

    /**
     * Clear all notifications.
     */
    public function clear(Request $request)
    {
        auth()->user()->notifications()->delete();
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()
            ->back()
            ->with('success', 'Tüm bildirimler temizlendi.');
    }

    /**
     * Register push token.
     */
    public function registerPushToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'platform' => 'required|in:web,ios,android',
            'device_id' => 'nullable|string',
            'device_name' => 'nullable|string',
        ]);
        
        $this->notificationService->registerPushToken(
            auth()->user(),
            $validated['token'],
            $validated['platform'],
            [
                'device_id' => $validated['device_id'] ?? null,
                'device_name' => $validated['device_name'] ?? null,
            ]
        );
        
        return response()->json(['success' => true]);
    }

    /**
     * Unregister push token.
     */
    public function unregisterPushToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);
        
        $this->notificationService->unregisterPushToken($validated['token']);
        
        return response()->json(['success' => true]);
    }

    /**
     * Test notification.
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'channel' => 'required|in:email,sms,push,database',
        ]);
        
        $user = auth()->user();
        
        // Queue test notification
        $this->notificationService->queue($user, 'test', [
            'subject' => 'Test Bildirimi',
            'content' => 'Bu bir test bildirimidir. Eğer bu mesajı aldıysanız, ' . $validated['channel'] . ' bildirimleri çalışıyor demektir.',
            'data' => [
                'test' => true,
                'timestamp' => now()->toDateTimeString(),
            ],
        ], $validated['channel']);
        
        // Process immediately for testing
        $this->notificationService->processQueue();
        
        return redirect()
            ->back()
            ->with('success', 'Test bildirimi gönderildi. Lütfen kontrol edin.');
    }

    /**
     * Get unread count (AJAX).
     */
    public function unreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (AJAX).
     */
    public function recent()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });
        
        return response()->json($notifications);
    }
}
