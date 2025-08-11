<?php

namespace App\Modules\Messages\Services;

use App\Modules\Messages\Models\MessageThread;
use App\Modules\Messages\Models\Message;
use App\Modules\Messages\Models\MessageThreadParticipant;
use App\Modules\Messages\Models\BlockedUser;
use App\Models\User;
use App\Modules\Products\Models\Product;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class MessageService
{
    /**
     * Create or get thread between users.
     */
    public function getOrCreateThread(User $user1, User $user2, array $data = []): MessageThread
    {
        // Check if thread exists between these users
        $thread = MessageThread::whereHas('participants', function ($query) use ($user1) {
            $query->where('user_id', $user1->id);
        })->whereHas('participants', function ($query) use ($user2) {
            $query->where('user_id', $user2->id);
        })->active()->first();
        
        if ($thread) {
            return $thread;
        }
        
        // Create new thread
        return $this->createThread([$user1, $user2], $data);
    }
    
    /**
     * Create a new thread.
     */
    public function createThread(array $users, array $data = []): MessageThread
    {
        DB::beginTransaction();
        try {
            // Create thread
            $thread = MessageThread::create(array_merge([
                'type' => 'general',
                'status' => 'active',
            ], $data));
            
            // Add participants
            foreach ($users as $user) {
                $role = $this->determineUserRole($user);
                $thread->addParticipant($user, $role);
            }
            
            // Log activity
            activity()
                ->performedOn($thread)
                ->causedBy(auth()->user())
                ->log('Yeni mesaj konuşması başlatıldı');
            
            DB::commit();
            
            return $thread;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    /**
     * Create product inquiry thread.
     */
    public function createProductInquiry(User $customer, Product $product, string $message): MessageThread
    {
        // Get vendor from product
        $vendorProduct = \App\Modules\VendorProducts\Models\VendorProduct::where('product_id', $product->id)
            ->first();
        
        if (!$vendorProduct) {
            throw new \Exception('Bu ürün için satıcı bulunamadı.');
        }
        
        $vendor = $vendorProduct->vendor;
        
        // Check if customer blocked vendor or vice versa
        if ($this->isBlocked($customer, $vendor)) {
            throw new \Exception('Bu kullanıcıya mesaj gönderemezsiniz.');
        }
        
        DB::beginTransaction();
        try {
            // Create thread
            $thread = $this->createThread([$customer, $vendor], [
                'subject' => "Ürün hakkında soru: {$product->name}",
                'product_id' => $product->id,
                'type' => 'product_inquiry',
            ]);
            
            // Send first message
            $this->sendMessage($thread, $customer, $message);
            
            DB::commit();
            
            return $thread;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    /**
     * Send message to thread.
     */
    public function sendMessage(MessageThread $thread, User $sender, string $message, array $options = []): Message
    {
        // Check if sender is participant
        if (!$thread->hasParticipant($sender)) {
            throw new \Exception('Bu konuşmaya mesaj gönderme yetkiniz yok.');
        }
        
        // Check if thread is active
        if ($thread->status !== 'active') {
            throw new \Exception('Bu konuşma aktif değil.');
        }
        
        DB::beginTransaction();
        try {
            // Create message
            $messageModel = $thread->sendMessage($sender, $message, $options['type'] ?? 'text', [
                'attachments' => $options['attachments'] ?? null,
                'offer_amount' => $options['offer_amount'] ?? null,
                'metadata' => $options['metadata'] ?? null,
            ]);
            
            // Notify other participants
            $this->notifyParticipants($thread, $messageModel, $sender);
            
            // Log activity
            activity()
                ->performedOn($messageModel)
                ->causedBy($sender)
                ->log('Mesaj gönderildi');
            
            DB::commit();
            
            return $messageModel;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    /**
     * Send offer message.
     */
    public function sendOffer(MessageThread $thread, User $sender, float $amount, string $message = null): Message
    {
        $offerMessage = $message ?? "₺{$amount} tutarında teklif";
        
        return $this->sendMessage($thread, $sender, $offerMessage, [
            'type' => 'offer',
            'offer_amount' => $amount,
        ]);
    }
    
    /**
     * Mark thread as read for user.
     */
    public function markThreadAsRead(MessageThread $thread, User $user): void
    {
        $thread->markAsReadForUser($user);
        
        // Mark all messages as read
        $messages = $thread->messages()
            ->where('sender_id', '!=', $user->id)
            ->get();
        
        foreach ($messages as $message) {
            $message->markAsReadBy($user);
        }
    }
    
    /**
     * Get user threads.
     */
    public function getUserThreads(User $user, array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = MessageThread::whereHas('participants', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('has_left', false);
        });
        
        // Apply filters
        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        } else {
            $query->active();
        }
        
        if (isset($filters['unread']) && $filters['unread']) {
            $query->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('unread_count', '>', 0);
            });
        }
        
        if (isset($filters['starred']) && $filters['starred']) {
            $query->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->where('is_starred', true);
            });
        }
        
        // Search
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('messages', function ($mq) use ($search) {
                        $mq->where('message', 'like', "%{$search}%");
                    })
                    ->orWhereHas('participants', function ($pq) use ($search) {
                        $pq->whereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%");
                        });
                    });
            });
        }
        
        return $query->with(['participants.user', 'product'])
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);
    }
    
    /**
     * Get thread messages.
     */
    public function getThreadMessages(MessageThread $thread, User $user): \Illuminate\Pagination\LengthAwarePaginator
    {
        // Check if user can view thread
        if (!$thread->hasParticipant($user)) {
            throw new \Exception('Bu konuşmayı görüntüleme yetkiniz yok.');
        }
        
        // Mark as read
        $this->markThreadAsRead($thread, $user);
        
        return $thread->messages()
            ->with(['sender', 'attachmentFiles'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(50);
    }
    
    /**
     * Star/unstar thread.
     */
    public function toggleStar(MessageThread $thread, User $user): bool
    {
        $participant = $thread->participants()
            ->where('user_id', $user->id)
            ->first();
        
        if (!$participant) {
            throw new \Exception('Bu konuşmaya katılımcı değilsiniz.');
        }
        
        $participant->update([
            'is_starred' => !$participant->is_starred,
        ]);
        
        return $participant->is_starred;
    }
    
    /**
     * Mute/unmute thread.
     */
    public function toggleMute(MessageThread $thread, User $user): bool
    {
        $participant = $thread->participants()
            ->where('user_id', $user->id)
            ->first();
        
        if (!$participant) {
            throw new \Exception('Bu konuşmaya katılımcı değilsiniz.');
        }
        
        $participant->update([
            'is_muted' => !$participant->is_muted,
        ]);
        
        return $participant->is_muted;
    }
    
    /**
     * Leave thread.
     */
    public function leaveThread(MessageThread $thread, User $user): void
    {
        $thread->removeParticipant($user);
        
        // Send system message
        $thread->sendMessage($user, "{$user->name} konuşmadan ayrıldı.", 'system');
    }
    
    /**
     * Archive thread.
     */
    public function archiveThread(MessageThread $thread, User $user): void
    {
        // Check if user is participant
        if (!$thread->hasParticipant($user)) {
            throw new \Exception('Bu konuşmayı arşivleme yetkiniz yok.');
        }
        
        $thread->archive();
        
        // Log activity
        activity()
            ->performedOn($thread)
            ->causedBy($user)
            ->log('Konuşma arşivlendi');
    }
    
    /**
     * Block user.
     */
    public function blockUser(User $blocker, User $blocked, string $reason = null): void
    {
        BlockedUser::firstOrCreate(
            [
                'user_id' => $blocker->id,
                'blocked_user_id' => $blocked->id,
            ],
            [
                'reason' => $reason,
                'blocked_at' => now(),
            ]
        );
        
        // Archive all threads between these users
        $threads = MessageThread::whereHas('participants', function ($q) use ($blocker) {
            $q->where('user_id', $blocker->id);
        })->whereHas('participants', function ($q) use ($blocked) {
            $q->where('user_id', $blocked->id);
        })->get();
        
        foreach ($threads as $thread) {
            $thread->block();
        }
        
        // Log activity
        activity()
            ->causedBy($blocker)
            ->withProperties(['blocked_user' => $blocked->name])
            ->log('Kullanıcı engellendi');
    }
    
    /**
     * Unblock user.
     */
    public function unblockUser(User $blocker, User $blocked): void
    {
        BlockedUser::where('user_id', $blocker->id)
            ->where('blocked_user_id', $blocked->id)
            ->update(['unblocked_at' => now()]);
        
        // Log activity
        activity()
            ->causedBy($blocker)
            ->withProperties(['unblocked_user' => $blocked->name])
            ->log('Kullanıcı engeli kaldırıldı');
    }
    
    /**
     * Check if user is blocked.
     */
    public function isBlocked(User $user1, User $user2): bool
    {
        return BlockedUser::where(function ($q) use ($user1, $user2) {
            $q->where('user_id', $user1->id)
                ->where('blocked_user_id', $user2->id);
        })->orWhere(function ($q) use ($user1, $user2) {
            $q->where('user_id', $user2->id)
                ->where('blocked_user_id', $user1->id);
        })->whereNull('unblocked_at')->exists();
    }
    
    /**
     * Get blocked users.
     */
    public function getBlockedUsers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereIn('id', function ($query) use ($user) {
            $query->select('blocked_user_id')
                ->from('blocked_users')
                ->where('user_id', $user->id)
                ->whereNull('unblocked_at');
        })->get();
    }
    
    /**
     * Determine user role in messaging.
     */
    private function determineUserRole(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }
        
        if ($user->hasRole('vendor')) {
            return 'vendor';
        }
        
        return 'customer';
    }
    
    /**
     * Notify participants about new message.
     */
    private function notifyParticipants(MessageThread $thread, Message $message, User $sender): void
    {
        $participants = $thread->participants()
            ->where('user_id', '!=', $sender->id)
            ->where('has_left', false)
            ->where('is_muted', false)
            ->with('user')
            ->get();
        
        foreach ($participants as $participant) {
            $participant->user->notify(new NewMessageNotification($thread, $message));
        }
    }
}
