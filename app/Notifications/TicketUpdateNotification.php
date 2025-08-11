<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\Models\TicketResponse;

class TicketUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $ticket;
    protected $response;
    protected $updateType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, TicketResponse $response = null, string $updateType = 'new_response')
    {
        $this->ticket = $ticket;
        $this->response = $response;
        $this->updateType = $updateType; // new_response, status_changed, assigned, resolved
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        
        // Check user's notification settings
        $settings = $notifiable->notificationSettings;
        
        if ($settings && $settings->email_enabled && $settings->email_tickets) {
            $channels[] = 'mail';
        }
        
        if ($settings && $settings->push_enabled) {
            $channels[] = 'broadcast';
        }
        
        // SMS for urgent tickets only
        if ($settings && $settings->sms_enabled && $this->ticket->priority === 'urgent') {
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = match($this->updateType) {
            'new_response' => "Destek talebinize cevap verildi - #{$this->ticket->ticket_number}",
            'status_changed' => "Destek talebi durumu güncellendi - #{$this->ticket->ticket_number}",
            'assigned' => "Destek talebiniz atandı - #{$this->ticket->ticket_number}",
            'resolved' => "Destek talebiniz çözüldü - #{$this->ticket->ticket_number}",
            default => "Destek talebi güncellendi - #{$this->ticket->ticket_number}",
        };
        
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Merhaba ' . $notifiable->name . ',');
        
        switch ($this->updateType) {
            case 'new_response':
                $mail->line('Destek talebinize yeni bir cevap verildi.')
                    ->line('Cevap: ' . Str::limit($this->response->message, 200));
                break;
                
            case 'status_changed':
                $mail->line('Destek talebinizin durumu güncellendi.')
                    ->line('Yeni durum: ' . $this->ticket->status_label);
                break;
                
            case 'assigned':
                $mail->line('Destek talebiniz bir temsilciye atandı.')
                    ->line('Temsilci: ' . $this->ticket->assignedTo->name);
                break;
                
            case 'resolved':
                $mail->line('Destek talebiniz çözüldü olarak işaretlendi.')
                    ->line('Memnuniyetinizi değerlendirmeyi unutmayın.');
                break;
        }
        
        return $mail->action('Destek Talebini Görüntüle', url('/tickets/' . $this->ticket->id))
            ->line('Destek ekibimiz size yardımcı olmak için burada!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $data = [
            'type' => 'ticket_update',
            'update_type' => $this->updateType,
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'ticket_subject' => $this->ticket->subject,
            'ticket_status' => $this->ticket->status,
            'ticket_priority' => $this->ticket->priority,
            'url' => '/tickets/' . $this->ticket->id,
        ];
        
        if ($this->response) {
            $data['response_id'] = $this->response->id;
            $data['response_preview'] = Str::limit($this->response->message, 100);
            $data['responder_name'] = $this->response->user->name;
        }
        
        return $data;
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable): array
    {
        $text = "ACİL Destek Talebi #{$this->ticket->ticket_number}: ";
        
        switch ($this->updateType) {
            case 'new_response':
                $text .= "Yeni cevap var";
                break;
            case 'resolved':
                $text .= "Çözüldü";
                break;
            default:
                $text .= "Güncellendi";
        }
        
        return [
            'to' => $notifiable->notificationSettings->sms_phone,
            'text' => $text,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): array
    {
        return [
            'type' => 'ticket_update',
            'update_type' => $this->updateType,
            'ticket_number' => $this->ticket->ticket_number,
            'message' => $this->getUpdateMessage(),
        ];
    }

    /**
     * Get update message for broadcast.
     */
    private function getUpdateMessage(): string
    {
        return match($this->updateType) {
            'new_response' => "Destek talebinize cevap verildi",
            'status_changed' => "Destek talebi durumu: {$this->ticket->status_label}",
            'assigned' => "Destek talebiniz atandı",
            'resolved' => "Destek talebiniz çözüldü",
            default => "Destek talebiniz güncellendi",
        };
    }
}
