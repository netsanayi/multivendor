<?php

namespace App\Modules\Messages\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Messages\Models\MessageThread;
use App\Modules\Messages\Services\MessageService;
use App\Modules\Products\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->middleware('auth');
    }

    /**
     * Display user's message threads.
     */
    public function index(Request $request)
    {
        $filters = [
            'type' => $request->type,
            'status' => $request->status,
            'unread' => $request->unread,
            'starred' => $request->starred,
            'search' => $request->search,
        ];
        
        $threads = $this->messageService->getUserThreads(auth()->user(), $filters);
        
        return view('messages.index', compact('threads'));
    }

    /**
     * Show message thread.
     */
    public function show(MessageThread $thread)
    {
        try {
            $messages = $this->messageService->getThreadMessages($thread, auth()->user());
            
            return view('messages.show', compact('thread', 'messages'));
        } catch (\Exception $e) {
            abort(403, $e->getMessage());
        }
    }

    /**
     * Start new conversation.
     */
    public function create(Request $request)
    {
        // If product inquiry
        if ($request->has('product_id')) {
            $product = Product::findOrFail($request->product_id);
            
            // Get vendor
            $vendorProduct = \App\Modules\VendorProducts\Models\VendorProduct::where('product_id', $product->id)
                ->first();
            
            if (!$vendorProduct) {
                return redirect()
                    ->back()
                    ->with('error', 'Bu ürün için satıcı bulunamadı.');
            }
            
            return view('messages.create', [
                'recipient' => $vendorProduct->vendor,
                'product' => $product,
                'type' => 'product_inquiry',
            ]);
        }
        
        // If specific user
        if ($request->has('user_id')) {
            $recipient = User::findOrFail($request->user_id);
            
            // Check if blocked
            if ($this->messageService->isBlocked(auth()->user(), $recipient)) {
                return redirect()
                    ->back()
                    ->with('error', 'Bu kullanıcıya mesaj gönderemezsiniz.');
            }
            
            return view('messages.create', [
                'recipient' => $recipient,
                'type' => 'general',
            ]);
        }
        
        return view('messages.create');
    }

    /**
     * Store new message thread.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required_without:product_id|exists:users,id',
            'product_id' => 'required_without:recipient_id|exists:products,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|in:product_inquiry,order_inquiry,general,offer',
            'offer_amount' => 'nullable|required_if:type,offer|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            // Product inquiry
            if (isset($validated['product_id'])) {
                $product = Product::findOrFail($validated['product_id']);
                $thread = $this->messageService->createProductInquiry(
                    auth()->user(),
                    $product,
                    $validated['message']
                );
            }
            // Direct message
            else {
                $recipient = User::findOrFail($validated['recipient_id']);
                
                // Check if blocked
                if ($this->messageService->isBlocked(auth()->user(), $recipient)) {
                    throw new \Exception('Bu kullanıcıya mesaj gönderemezsiniz.');
                }
                
                $thread = $this->messageService->getOrCreateThread(
                    auth()->user(),
                    $recipient,
                    [
                        'subject' => $validated['subject'],
                        'type' => $validated['type'] ?? 'general',
                    ]
                );
                
                // Send message
                if ($validated['type'] === 'offer' && isset($validated['offer_amount'])) {
                    $this->messageService->sendOffer(
                        $thread,
                        auth()->user(),
                        $validated['offer_amount'],
                        $validated['message']
                    );
                } else {
                    $this->messageService->sendMessage(
                        $thread,
                        auth()->user(),
                        $validated['message']
                    );
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('messages.show', $thread)
                ->with('success', 'Mesajınız gönderildi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Send message to thread.
     */
    public function send(Request $request, MessageThread $thread)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'type' => 'nullable|in:text,offer',
            'offer_amount' => 'nullable|required_if:type,offer|numeric|min:0',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);
        
        try {
            $options = [];
            
            // Handle offer
            if ($validated['type'] === 'offer' && isset($validated['offer_amount'])) {
                $message = $this->messageService->sendOffer(
                    $thread,
                    auth()->user(),
                    $validated['offer_amount'],
                    $validated['message']
                );
            } else {
                // Handle attachments
                if ($request->hasFile('attachments')) {
                    $attachments = [];
                    foreach ($request->file('attachments') as $file) {
                        $path = $file->store('messages/' . $thread->id, 'public');
                        $attachments[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'type' => $file->getMimeType(),
                            'size' => $file->getSize(),
                        ];
                    }
                    $options['attachments'] = $attachments;
                    $options['type'] = str_contains($attachments[0]['type'], 'image') ? 'image' : 'file';
                }
                
                $message = $this->messageService->sendMessage(
                    $thread,
                    auth()->user(),
                    $validated['message'],
                    $options
                );
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }
            
            return redirect()
                ->route('messages.show', $thread)
                ->with('success', 'Mesajınız gönderildi.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 422);
            }
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Star/unstar thread.
     */
    public function toggleStar(Request $request, MessageThread $thread)
    {
        try {
            $isStarred = $this->messageService->toggleStar($thread, auth()->user());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_starred' => $isStarred,
                ]);
            }
            
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 422);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Mute/unmute thread.
     */
    public function toggleMute(Request $request, MessageThread $thread)
    {
        try {
            $isMuted = $this->messageService->toggleMute($thread, auth()->user());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'is_muted' => $isMuted,
                ]);
            }
            
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], 422);
            }
            
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Archive thread.
     */
    public function archive(MessageThread $thread)
    {
        try {
            $this->messageService->archiveThread($thread, auth()->user());
            
            return redirect()
                ->route('messages.index')
                ->with('success', 'Konuşma arşivlendi.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Leave thread.
     */
    public function leave(MessageThread $thread)
    {
        $this->messageService->leaveThread($thread, auth()->user());
        
        return redirect()
            ->route('messages.index')
            ->with('success', 'Konuşmadan ayrıldınız.');
    }

    /**
     * Block user.
     */
    public function blockUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);
        
        $userToBlock = User::findOrFail($validated['user_id']);
        
        $this->messageService->blockUser(
            auth()->user(),
            $userToBlock,
            $validated['reason']
        );
        
        return redirect()
            ->route('messages.index')
            ->with('success', 'Kullanıcı engellendi.');
    }

    /**
     * Unblock user.
     */
    public function unblockUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $userToUnblock = User::findOrFail($validated['user_id']);
        
        $this->messageService->unblockUser(auth()->user(), $userToUnblock);
        
        return redirect()
            ->back()
            ->with('success', 'Kullanıcı engeli kaldırıldı.');
    }

    /**
     * Show blocked users.
     */
    public function blockedUsers()
    {
        $blockedUsers = $this->messageService->getBlockedUsers(auth()->user());
        
        return view('messages.blocked', compact('blockedUsers'));
    }

    /**
     * Accept offer.
     */
    public function acceptOffer(Request $request, MessageThread $thread, $messageId)
    {
        $message = $thread->messages()->findOrFail($messageId);
        
        if ($message->type !== 'offer') {
            return redirect()->back()->with('error', 'Bu mesaj bir teklif değil.');
        }
        
        if ($message->offer_status !== 'pending') {
            return redirect()->back()->with('error', 'Bu teklif zaten işlem görmüş.');
        }
        
        $message->acceptOffer();
        
        // Send system message
        $this->messageService->sendMessage(
            $thread,
            auth()->user(),
            "Teklif kabul edildi: ₺{$message->offer_amount}",
            ['type' => 'system']
        );
        
        return redirect()
            ->back()
            ->with('success', 'Teklif kabul edildi.');
    }

    /**
     * Reject offer.
     */
    public function rejectOffer(Request $request, MessageThread $thread, $messageId)
    {
        $message = $thread->messages()->findOrFail($messageId);
        
        if ($message->type !== 'offer') {
            return redirect()->back()->with('error', 'Bu mesaj bir teklif değil.');
        }
        
        if ($message->offer_status !== 'pending') {
            return redirect()->back()->with('error', 'Bu teklif zaten işlem görmüş.');
        }
        
        $message->rejectOffer();
        
        // Send system message
        $this->messageService->sendMessage(
            $thread,
            auth()->user(),
            "Teklif reddedildi: ₺{$message->offer_amount}",
            ['type' => 'system']
        );
        
        return redirect()
            ->back()
            ->with('success', 'Teklif reddedildi.');
    }
}
