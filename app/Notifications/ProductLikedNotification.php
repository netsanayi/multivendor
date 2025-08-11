<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Modules\Products\Models\Product;

class ProductLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $product;
    protected $likedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Product $product, $likedBy)
    {
        $this->product = $product;
        $this->likedBy = $likedBy;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];
        
        // Check user's notification settings
        $settings = $notifiable->notificationSettings;
        
        if ($settings && $settings->email_enabled && $settings->email_product_updates) {
            $channels[] = 'mail';
        }
        
        if ($settings && $settings->push_enabled) {
            $channels[] = 'broadcast';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ürününüz beğenildi!')
            ->greeting('Merhaba ' . $notifiable->name . ',')
            ->line($this->likedBy->name . ' adlı kullanıcı "' . $this->product->name . '" ürününüzü beğendi.')
            ->action('Ürünü Görüntüle', url('/vendor/products/' . $this->product->id))
            ->line('Ürünlerinize gösterilen ilgi için teşekkür ederiz!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'product_liked',
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'liked_by_id' => $this->likedBy->id,
            'liked_by_name' => $this->likedBy->name,
            'message' => $this->likedBy->name . ' ürününüzü beğendi: ' . $this->product->name,
            'url' => '/vendor/products/' . $this->product->id,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): array
    {
        return [
            'type' => 'product_liked',
            'product_name' => $this->product->name,
            'liked_by' => $this->likedBy->name,
            'message' => $this->likedBy->name . ' ürününüzü beğendi',
        ];
    }
}
