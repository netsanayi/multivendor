<?php

namespace App\Modules\Tickets\Services;

use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\Models\TicketCategory;
use App\Modules\Tickets\Models\TicketResponse;
use App\Modules\Tickets\Models\TicketTemplate;
use App\Models\User;
use App\Notifications\TicketUpdateNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TicketService
{
    /**
     * Create a new ticket.
     */
    public function createTicket(array $data, User $user): Ticket
    {
        DB::beginTransaction();
        try {
            // Determine user type
            if (!isset($data['user_type'])) {
                $data['user_type'] = $user->hasRole('vendor') ? 'vendor' : 'customer';
            }
            
            // Create ticket
            $ticket = Ticket::create(array_merge($data, [
                'user_id' => $user->id,
                'ticket_number' => Ticket::generateTicketNumber(),
                'status' => 'open',
                'last_activity_at' => now(),
            ]));
            
            // Auto-assign based on category
            if ($ticket->category && $ticket->category->default_assignee) {
                $ticket->assignTo($ticket->category->default_assignee);
            }
            
            // Notify admins
            $this->notifyAdminsAboutNewTicket($ticket);
            
            // Log activity
            activity()
                ->performedOn($ticket)
                ->causedBy($user)
                ->withProperties(['ticket_number' => $ticket->ticket_number])
                ->log('Yeni destek talebi oluşturuldu');
            
            DB::commit();
            
            return $ticket;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    /**
     * Add response to ticket.
     */
    public function addResponse(Ticket $ticket, string $message, User $user, array $options = []): TicketResponse
    {
        DB::beginTransaction();
        try {
            $isInternal = $options['is_internal'] ?? false;
            $isSolution = $options['is_solution'] ?? false;
            $attachments = $options['attachments'] ?? null;
            
            // Create response
            $response = $ticket->responses()->create([
                'user_id' => $user->id,
                'message' => $message,
                'is_internal' => $isInternal,
                'is_solution' => $isSolution,
                'attachments' => $attachments,
            ]);
            
            // Update ticket
            $ticket->increment('response_count');
            $ticket->update([
                'last_activity_at' => now(),
                'status' => $this->determineNewStatus($ticket, $user, $isSolution),
            ]);
            
            // Notify relevant users
            if (!$isInternal) {
                $this->notifyUsersAboutResponse($ticket, $response);
            }
            
            // Log activity
            activity()
                ->performedOn($ticket)
                ->causedBy($user)
                ->withProperties(['response_id' => $response->id])
                ->log($isInternal ? 'İç not eklendi' : 'Cevap eklendi');
            
            DB::commit();
            
            return $response;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    
    /**
     * Assign ticket to user.
     */
    public function assignTicket(Ticket $ticket, User $assignee): void
    {
        $oldAssignee = $ticket->assignedTo;
        
        $ticket->assignTo($assignee);
        
        // Notify new assignee
        $assignee->notify(new TicketUpdateNotification($ticket, null, 'assigned'));
        
        // Log activity
        activity()
            ->performedOn($ticket)
            ->causedBy(auth()->user())
            ->withProperties([
                'old_assignee' => $oldAssignee ? $oldAssignee->name : null,
                'new_assignee' => $assignee->name,
            ])
            ->log('Ticket atandı');
    }
    
    /**
     * Update ticket status.
     */
    public function updateStatus(Ticket $ticket, string $status): void
    {
        $oldStatus = $ticket->status;
        
        $ticket->updateStatus($status);
        
        // Notify ticket owner
        $ticket->user->notify(new TicketUpdateNotification($ticket, null, 'status_changed'));
        
        // If resolved, ask for satisfaction rating
        if ($status === 'resolved') {
            $this->sendSatisfactionSurvey($ticket);
        }
    }
    
    /**
     * Close ticket.
     */
    public function closeTicket(Ticket $ticket, string $reason = null): void
    {
        $ticket->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
        
        if ($reason) {
            $this->addResponse($ticket, "Ticket kapatıldı. Sebep: {$reason}", auth()->user(), [
                'is_internal' => true,
            ]);
        }
        
        // Log activity
        activity()
            ->performedOn($ticket)
            ->causedBy(auth()->user())
            ->withProperties(['reason' => $reason])
            ->log('Ticket kapatıldı');
    }
    
    /**
     * Reopen ticket.
     */
    public function reopenTicket(Ticket $ticket): void
    {
        $ticket->update([
            'status' => 'open',
            'closed_at' => null,
        ]);
        
        // Log activity
        activity()
            ->performedOn($ticket)
            ->causedBy(auth()->user())
            ->log('Ticket yeniden açıldı');
    }
    
    /**
     * Apply template to response.
     */
    public function applyTemplate(TicketTemplate $template, array $variables = []): string
    {
        $content = $template->content;
        
        // Replace variables
        foreach ($variables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
        }
        
        // Increment usage count
        $template->increment('usage_count');
        
        return $content;
    }
    
    /**
     * Get ticket statistics.
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Ticket::query();
        
        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }
        
        return [
            'total' => $query->count(),
            'open' => $query->clone()->open()->count(),
            'closed' => $query->clone()->closed()->count(),
            'urgent' => $query->clone()->urgent()->count(),
            'unassigned' => $query->clone()->unassigned()->count(),
            'avg_response_time' => $this->calculateAverageResponseTime($query->clone()),
            'avg_resolution_time' => $this->calculateAverageResolutionTime($query->clone()),
            'satisfaction_rating' => $query->clone()->whereNotNull('satisfaction_rating')->avg('satisfaction_rating'),
            'by_category' => $this->getStatsByCategory($query->clone()),
            'by_priority' => $this->getStatsByPriority($query->clone()),
            'by_user_type' => $this->getStatsByUserType($query->clone()),
        ];
    }
    
    /**
     * Search tickets.
     */
    public function searchTickets(string $query, User $user = null): \Illuminate\Database\Eloquent\Collection
    {
        $tickets = Ticket::where(function ($q) use ($query) {
            $q->where('ticket_number', 'like', "%{$query}%")
                ->orWhere('subject', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        });
        
        // Filter by user permissions
        if ($user && !$user->hasRole('admin')) {
            $tickets->where(function ($q) use ($user) {
                $q->where('user_id', $user->id);
                
                if ($user->hasRole('vendor')) {
                    // Vendor can see tickets related to their products
                    $productIds = \App\Modules\VendorProducts\Models\VendorProduct::where('vendor_id', $user->id)
                        ->pluck('product_id');
                    
                    $q->orWhereIn('related_product_id', $productIds);
                }
            });
        }
        
        return $tickets->with(['category', 'user', 'assignedTo'])
            ->latest()
            ->limit(20)
            ->get();
    }
    
    /**
     * Determine new status after response.
     */
    private function determineNewStatus(Ticket $ticket, User $user, bool $isSolution): string
    {
        if ($isSolution) {
            return 'resolved';
        }
        
        if ($user->hasRole('admin')) {
            return 'answered';
        }
        
        if ($user->id === $ticket->user_id) {
            return 'pending';
        }
        
        return $ticket->status;
    }
    
    /**
     * Notify admins about new ticket.
     */
    private function notifyAdminsAboutNewTicket(Ticket $ticket): void
    {
        $admins = User::role('admin')->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new TicketUpdateNotification($ticket, null, 'new_ticket'));
        }
    }
    
    /**
     * Notify users about response.
     */
    private function notifyUsersAboutResponse(Ticket $ticket, TicketResponse $response): void
    {
        // Notify ticket owner
        if ($response->user_id !== $ticket->user_id) {
            $ticket->user->notify(new TicketUpdateNotification($ticket, $response, 'new_response'));
        }
        
        // Notify assigned user
        if ($ticket->assigned_to && $response->user_id !== $ticket->assigned_to) {
            $ticket->assignedTo->notify(new TicketUpdateNotification($ticket, $response, 'new_response'));
        }
        
        // Notify vendor if related to their product
        if ($ticket->related_product_id) {
            $vendorProduct = \App\Modules\VendorProducts\Models\VendorProduct::where('product_id', $ticket->related_product_id)
                ->first();
            
            if ($vendorProduct && $vendorProduct->vendor_id !== $response->user_id) {
                $vendorProduct->vendor->notify(new TicketUpdateNotification($ticket, $response, 'new_response'));
            }
        }
    }
    
    /**
     * Send satisfaction survey.
     */
    private function sendSatisfactionSurvey(Ticket $ticket): void
    {
        // Send email with satisfaction survey link
        // This would be implemented with a specific notification
    }
    
    /**
     * Calculate average response time.
     */
    private function calculateAverageResponseTime($query): ?float
    {
        $tickets = $query->has('responses')->get();
        
        if ($tickets->isEmpty()) {
            return null;
        }
        
        $totalTime = 0;
        $count = 0;
        
        foreach ($tickets as $ticket) {
            $firstResponse = $ticket->responses()->orderBy('created_at')->first();
            if ($firstResponse) {
                $totalTime += $ticket->created_at->diffInMinutes($firstResponse->created_at);
                $count++;
            }
        }
        
        return $count > 0 ? round($totalTime / $count, 2) : null;
    }
    
    /**
     * Calculate average resolution time.
     */
    private function calculateAverageResolutionTime($query): ?float
    {
        $tickets = $query->whereNotNull('closed_at')->get();
        
        if ($tickets->isEmpty()) {
            return null;
        }
        
        $totalTime = 0;
        $count = 0;
        
        foreach ($tickets as $ticket) {
            $totalTime += $ticket->created_at->diffInHours($ticket->closed_at);
            $count++;
        }
        
        return $count > 0 ? round($totalTime / $count, 2) : null;
    }
    
    /**
     * Get statistics by category.
     */
    private function getStatsByCategory($query): array
    {
        return $query->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name ?? 'Uncategorized' => $item->count];
            })
            ->toArray();
    }
    
    /**
     * Get statistics by priority.
     */
    private function getStatsByPriority($query): array
    {
        return $query->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->priority => $item->count];
            })
            ->toArray();
    }
    
    /**
     * Get statistics by user type.
     */
    private function getStatsByUserType($query): array
    {
        return $query->select('user_type', DB::raw('count(*) as count'))
            ->groupBy('user_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->user_type => $item->count];
            })
            ->toArray();
    }
}
