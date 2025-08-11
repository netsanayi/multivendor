<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Yeni Mesaj',
                'slug' => 'new-message',
                'description' => 'Kullanıcıya yeni mesaj geldiğinde gönderilir',
                'channel' => 'email',
                'subject' => 'Yeni mesajınız var - {sender_name}',
                'content' => "Merhaba {user_name},\n\n{sender_name} size bir mesaj gönderdi:\n\n{message_preview}\n\nTüm mesajı görüntülemek için tıklayın: {url}",
                'variables' => json_encode(['user_name', 'sender_name', 'message_preview', 'url']),
                'is_active' => true,
            ],
            [
                'name' => 'Ticket Cevaplandı',
                'slug' => 'ticket-responded',
                'description' => 'Destek talebine cevap verildiğinde gönderilir',
                'channel' => 'email',
                'subject' => 'Destek talebinize cevap verildi - #{ticket_number}',
                'content' => "Merhaba {user_name},\n\n#{ticket_number} numaralı destek talebinize cevap verildi.\n\nCevap: {response_preview}\n\nDetaylar için: {url}",
                'variables' => json_encode(['user_name', 'ticket_number', 'response_preview', 'url']),
                'is_active' => true,
            ],
            [
                'name' => 'Ürün Beğenildi',
                'slug' => 'product-liked',
                'description' => 'Ürün beğenildiğinde satıcıya gönderilir',
                'channel' => 'push',
                'subject' => null,
                'content' => '{user_name} ürününüzü beğendi: {product_name}',
                'variables' => json_encode(['user_name', 'product_name']),
                'is_active' => true,
            ],
            [
                'name' => 'Sipariş Onaylandı',
                'slug' => 'order-confirmed',
                'description' => 'Sipariş onaylandığında müşteriye gönderilir',
                'channel' => 'email',
                'subject' => 'Siparişiniz onaylandı - #{order_number}',
                'content' => "Merhaba {user_name},\n\n#{order_number} numaralı siparişiniz onaylandı.\n\nSipariş tutarı: {order_total}\nTahmini teslimat: {estimated_delivery}\n\nSipariş detayları: {url}",
                'variables' => json_encode(['user_name', 'order_number', 'order_total', 'estimated_delivery', 'url']),
                'is_active' => true,
            ],
            [
                'name' => 'Fiyat Uyarısı',
                'slug' => 'price-alert',
                'description' => 'İstek listesindeki ürünün fiyatı düştüğünde gönderilir',
                'channel' => 'email',
                'subject' => 'Fiyat düştü! {product_name}',
                'content' => "Merhaba {user_name},\n\nİstek listenizdeki '{product_name}' ürününün fiyatı düştü!\n\nEski fiyat: {old_price}\nYeni fiyat: {new_price}\nİndirim: {discount_percentage}%\n\nŞimdi satın al: {url}",
                'variables' => json_encode(['user_name', 'product_name', 'old_price', 'new_price', 'discount_percentage', 'url']),
                'is_active' => true,
            ]
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['slug' => $template['slug']],
                array_merge($template, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Bildirim template\'leri başarıyla oluşturuldu.');
    }
}