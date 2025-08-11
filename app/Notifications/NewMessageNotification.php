<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Modules\Messages\Models\MessageThread;
use App\Modules\Messages\Models\Message;

class NewMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $thread;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(MessageThread $thread, Message $message)
    {
        $this->thread = $thread;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        
        // Check user's notification settings
        $settings = $notifiable->notificationSettings;
        
        if ($settings && $settings->email_enabled && $settings->email_messages) {
            $channels[] = 'mail';
        }
        
        if ($settings && $settings->push_enabled && $settings->push_messages) {
            $channels[] = 'broadcast';
        }
        
        if ($settings && $settings->sms_enabled && $this->message->type === 'offer') {
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->message->type === 'offer' 
            ? 'Yeni teklif aldınız!' 
            : 'Yeni mesajınız var!';
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->message->sender->name . ' size bir mesaj gönderdi.')
            ->line('Mesaj: ' . Str::limit($this->message->message, 100))
            ->action('Mesajı Görüntüle', url('/messages/' . $this->thread->id))
            ->line('Hızlı cevap vermek için yukarıdaki butona tıklayın.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_message',
            'thread_id' => $this->thread->id,
            'message_id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'message_type' => $this->message->type,
            'message_preview' => Str::limit($this->message->message, 50),
            'offer_amount' => $this->message->offer_amount,
            'url' => '/messages/' . $this->thread->id,
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms($notifiable): array
    {
        $text = $this->message->type === 'offer'
            ? "Yeni teklif: ₺{$this->message->offer_amount} - {$this->message->sender->name}"
            : "Yeni mesaj: " . Str::limit($this->message->message, 50);
        
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
            'type' => 'new_message',
            'sender' => $this->message->sender->name,
            'preview' => Str::limit($this->message->message, 50),
            'thread_id' => $this->thread->id,
        ];
    }
}
