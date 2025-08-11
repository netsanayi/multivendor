<?php

namespace App\Modules\Tickets\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\Models\TicketCategory;
use App\Modules\Tickets\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
        $this->middleware('auth');
    }

    /**
     * Display user's tickets.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Ticket::with(['category', 'assignedTo', 'product']);
        
        // Admin sees all tickets
        if (!$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                
                // Vendor sees tickets for their products
                if ($user->hasRole('vendor')) {
                    $productIds = \App\Modules\VendorProducts\Models\VendorProduct::where('vendor_id', $user->id)
                        ->pluck('product_id');
                    
                    $q->orWhereIn('related_product_id', $productIds);
                }
            });
        }
        
        // Filters
        if ($request->has('status')) {
            if ($request->status === 'open') {
                $query->open();
            } elseif ($request->status === 'closed') {
                $query->closed();
            } else {
                $query->where('status', $request->status);
            }
        }
        
        if ($request->has('priority')) {
            $query->priority($request->priority);
        }
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }
        
        $tickets = $query->latest()->paginate(20);
        $categories = TicketCategory::active()->ordered()->get();
        
        return view('tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Show ticket creation form.
     */
    public function create()
    {
        $categories = TicketCategory::active()->ordered()->get();
        
        // Get user's products if vendor
        $products = null;
        if (auth()->user()->hasRole('vendor')) {
            $products = \App\Modules\VendorProducts\Models\VendorProduct::where('vendor_id', auth()->id())
                ->with('product')
                ->get()
                ->pluck('product');
        }
        
        return view('tickets.create', compact('categories', 'products'));
    }

    /**
     * Store new ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'related_product_id' => 'nullable|exists:products,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max
        ]);
        
        DB::beginTransaction();
        try {
            $ticket = $this->ticketService->createTicket($validated, auth()->user());
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('tickets/' . $ticket->id, 'public');
                    
                    $ticket->attachments()->create([
                        'uploaded_by' => auth()->id(),
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('tickets.show', $ticket)
                ->with('success', 'Destek talebiniz oluşturuldu. Ticket No: ' . $ticket->ticket_number);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Destek talebi oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Show ticket details.
     */
    public function show(Ticket $ticket)
    {
        // Check permission
        if (!$ticket->canBeViewedBy(auth()->user())) {
            abort(403, 'Bu destek talebini görüntüleme yetkiniz yok.');
        }
        
        $responses = $ticket->responses()
            ->with(['user', 'attachmentFiles']);
        
        // Non-admins don't see internal notes
        if (!auth()->user()->hasRole('admin')) {
            $responses->public();
        }
        
        $responses = $responses->oldest()->get();
        
        return view('tickets.show', compact('ticket', 'responses'));
    }

    /**
     * Add response to ticket.
     */
    public function respond(Request $request, Ticket $ticket)
    {
        // Check permission
        if (!$ticket->canBeViewedBy(auth()->user())) {
            abort(403);
        }
        
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|boolean',
            'is_solution' => 'nullable|boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);
        
        // Only admins can add internal notes
        if (!auth()->user()->hasRole('admin')) {
            $validated['is_internal'] = false;
            $validated['is_solution'] = false;
        }
        
        DB::beginTransaction();
        try {
            $response = $this->ticketService->addResponse(
                $ticket,
                $validated['message'],
                auth()->user(),
                $validated
            );
            
            // Handle attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('tickets/' . $ticket->id, 'public');
                    
                    $ticket->attachments()->create([
                        'response_id' => $response->id,
                        'uploaded_by' => auth()->id(),
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('tickets.show', $ticket)
                ->with('success', 'Cevabınız eklendi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Cevap eklenirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Close ticket.
     */
    public function close(Request $request, Ticket $ticket)
    {
        // Check permission
        if ($ticket->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        
        if ($ticket->isClosed()) {
            return redirect()
                ->back()
                ->with('warning', 'Bu destek talebi zaten kapalı.');
        }
        
        $this->ticketService->closeTicket($ticket, $request->reason);
        
        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Destek talebi kapatıldı.');
    }

    /**
     * Reopen ticket.
     */
    public function reopen(Ticket $ticket)
    {
        // Check permission
        if ($ticket->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        
        if ($ticket->isOpen()) {
            return redirect()
                ->back()
                ->with('warning', 'Bu destek talebi zaten açık.');
        }
        
        $this->ticketService->reopenTicket($ticket);
        
        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Destek talebi yeniden açıldı.');
    }

    /**
     * Rate ticket satisfaction.
     */
    public function rate(Request $request, Ticket $ticket)
    {
        // Only ticket owner can rate
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);
        
        $ticket->rateSatisfaction($validated['rating'], $validated['comment']);
        
        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Değerlendirmeniz alındı. Teşekkür ederiz!');
    }
}
