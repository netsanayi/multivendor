# 📦 Ticket/Destek, Mesajlaşma ve Bildirim Sistemleri Dokümantasyonu

## 🎯 Genel Bakış

Bu dokümantasyon, Laravel Multi-Vendor Marketplace uygulamasına eklenen **Ticket/Destek**, **Mesajlaşma** ve **Bildirim** sistemlerinin kullanımını açıklar.

---

## 🎫 Ticket/Destek Sistemi

### Özellikler
- 📝 **Kategorize Destek Talepleri**: Farklı kategorilerde ticket oluşturma
- 🎯 **Öncelik Seviyeleri**: Düşük, Normal, Yüksek, Acil
- 👥 **Çoklu Kullanıcı Desteği**: Müşteri, Satıcı ve Yönetici rolleri
- 📎 **Dosya Ekleme**: Ticket'lara dosya ekleme desteği
- 💬 **İç Notlar**: Sadece yöneticilerin görebileceği notlar
- ✅ **Çözüm İşaretleme**: Cevapları çözüm olarak işaretleme
- ⭐ **Memnuniyet Değerlendirmesi**: 1-5 arası puanlama
- 🔍 **Gelişmiş Arama**: Ticket numarası, konu veya içeriğe göre arama

### Kullanım

#### Yeni Ticket Oluşturma
```php
use App\Modules\Tickets\Services\TicketService;

$ticketService = new TicketService();
$ticket = $ticketService->createTicket([
    'category_id' => 1,
    'subject' => 'Ürün ile ilgili sorun',
    'description' => 'Detaylı açıklama...',
    'priority' => 'high',
    'related_product_id' => 123, // Opsiyonel
], $user);
```

#### Ticket'a Cevap Ekleme
```php
$response = $ticketService->addResponse(
    $ticket,
    'Cevap mesajı',
    $user,
    [
        'is_internal' => false, // İç not değil
        'is_solution' => true,  // Çözüm olarak işaretle
    ]
);
```

#### Ticket Durumları
- `open`: Açık
- `pending`: Beklemede
- `answered`: Cevaplandı
- `on_hold`: Askıda
- `closed`: Kapalı
- `resolved`: Çözüldü

### Veritabanı Tabloları

#### tickets
- Temel ticket bilgileri
- Kullanıcı, kategori ve ürün ilişkileri
- Öncelik ve durum bilgileri

#### ticket_responses
- Ticket cevapları
- İç not ve çözüm işaretleri

#### ticket_categories
- Ticket kategorileri
- Renk kodları ve sıralama

#### ticket_templates
- Hazır cevap şablonları
- Değişken desteği

### API Endpoints

```
GET    /tickets                 # Ticket listesi
GET    /tickets/create          # Yeni ticket formu
POST   /tickets                 # Ticket oluştur
GET    /tickets/{id}            # Ticket detayı
POST   /tickets/{id}/respond    # Cevap ekle
POST   /tickets/{id}/close      # Ticket'ı kapat
POST   /tickets/{id}/reopen     # Ticket'ı yeniden aç
POST   /tickets/{id}/rate       # Memnuniyet değerlendirmesi
```

---

## 💬 Mesajlaşma Sistemi

### Özellikler
- 🗨️ **Gerçek Zamanlı Mesajlaşma**: Anlık mesaj gönderimi
- 📸 **Dosya Paylaşımı**: Resim ve dosya gönderimi
- 💰 **Teklif Sistemi**: Fiyat teklifi gönderme ve yönetme
- 🚫 **Engelleme**: Kullanıcı engelleme özelliği
- ⭐ **Yıldızlama**: Önemli konuşmaları işaretleme
- 🔕 **Sessize Alma**: Bildirim kapatma
- 📦 **Ürün Sorguları**: Ürünler hakkında soru sorma
- 👥 **Çoklu Katılımcı**: Grup konuşmaları (ileride)

### Kullanım

#### Yeni Konuşma Başlatma
```php
use App\Modules\Messages\Services\MessageService;

$messageService = new MessageService();

// İki kullanıcı arasında
$thread = $messageService->getOrCreateThread($user1, $user2, [
    'subject' => 'Konu başlığı',
    'type' => 'general',
]);

// Ürün hakkında soru
$thread = $messageService->createProductInquiry(
    $customer,
    $product,
    'Bu ürün hakkında bir sorum var...'
);
```

#### Mesaj Gönderme
```php
// Basit mesaj
$message = $messageService->sendMessage(
    $thread,
    $sender,
    'Mesaj içeriği'
);

// Teklif gönderme
$offer = $messageService->sendOffer(
    $thread,
    $sender,
    1500.00, // Teklif tutarı
    'Bu ürün için teklifim' // Opsiyonel mesaj
);
```

#### Mesaj Tipleri
- `text`: Metin mesajı
- `image`: Resim
- `file`: Dosya
- `offer`: Fiyat teklifi
- `system`: Sistem mesajı

### Veritabanı Tabloları

#### message_threads
- Konuşma bilgileri
- Ürün/sipariş ilişkileri
- Son mesaj ve mesaj sayısı

#### message_thread_participants
- Konuşma katılımcıları
- Okunmamış mesaj sayısı
- Yıldız ve sessize alma durumu

#### messages
- Mesaj içeriği
- Teklif bilgileri
- Düzenleme ve silme durumu

#### blocked_users
- Engellenmiş kullanıcılar
- Engelleme nedeni ve tarihi

### API Endpoints

```
GET    /messages                        # Konuşma listesi
GET    /messages/create                 # Yeni mesaj formu
POST   /messages                        # Yeni konuşma başlat
GET    /messages/{thread}               # Konuşma detayı
POST   /messages/{thread}/send          # Mesaj gönder
POST   /messages/{thread}/star          # Yıldızla/kaldır
POST   /messages/{thread}/mute          # Sessize al/aç
POST   /messages/{thread}/archive       # Arşivle
POST   /messages/{thread}/leave         # Konuşmadan ayrıl
POST   /messages/{thread}/offer/{id}/accept  # Teklifi kabul et
POST   /messages/{thread}/offer/{id}/reject  # Teklifi reddet
POST   /messages/block                  # Kullanıcı engelle
POST   /messages/unblock                # Engeli kaldır
GET    /messages/blocked                # Engellenen kullanıcılar
```

---

## 🔔 Bildirim Sistemi

### Özellikler
- 📧 **Email Bildirimleri**: Özelleştirilebilir email şablonları
- 📱 **SMS Bildirimleri**: SMS entegrasyonu
- 🔔 **Push Bildirimleri**: Web, iOS ve Android push desteği
- 💾 **Veritabanı Bildirimleri**: Uygulama içi bildirimler
- ⏰ **Sessiz Saatler**: Belirlenen saatlerde bildirim göndermeme
- 📊 **Özet Bildirimleri**: Günlük/haftalık/aylık özetler
- ⚙️ **Detaylı Ayarlar**: Kullanıcı bazlı bildirim tercihleri
- 📈 **İstatistikler**: Bildirim performans metrikleri

### Kullanım

#### Bildirim Gönderme
```php
use App\Notifications\ProductLikedNotification;
use App\Modules\Notifications\Services\NotificationService;

// Laravel Notification kullanarak
$vendor->notify(new ProductLikedNotification($product, $user));

// Service kullanarak
$notificationService = new NotificationService();
$notificationService->send($users, $notification);

// Kuyruğa ekleme
$notificationService->queue($user, 'order_shipped', [
    'subject' => 'Siparişiniz kargoya verildi',
    'content' => 'Sipariş detayları...',
    'data' => ['order_id' => 123],
], 'email', $scheduledAt);
```

#### Bildirim Kanalları
- `email`: Email bildirimi
- `sms`: SMS bildirimi
- `push`: Push notification
- `database`: Veritabanı bildirimi

#### Push Token Kaydetme
```javascript
// Web Push
navigator.serviceWorker.ready.then(function(registration) {
    registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: vapidPublicKey
    }).then(function(subscription) {
        // Token'ı sunucuya gönder
        axios.post('/notifications/push/register', {
            token: subscription.endpoint,
            platform: 'web'
        });
    });
});
```

### Bildirim Tipleri

#### Sistem Bildirimleri
- `product_liked`: Ürün beğenildi
- `new_message`: Yeni mesaj
- `ticket_update`: Destek talebi güncellendi
- `order_update`: Sipariş güncellendi
- `price_alert`: Fiyat uyarısı
- `wishlist_update`: Favori listesi güncellendi
- `vendor_update`: Satıcı güncelleme
- `system_announcement`: Sistem duyurusu

### Veritabanı Tabloları

#### notification_settings
- Kullanıcı bildirim tercihleri
- Kanal bazlı ayarlar
- Sessiz saatler ve zaman dilimi

#### notification_queue
- Gönderilecek bildirimler
- Öncelik ve zamanlama
- Deneme sayısı ve hata mesajları

#### notification_history
- Gönderilmiş bildirimler
- Okunma ve tıklanma durumu
- Performans metrikleri

#### push_tokens
- Push notification token'ları
- Platform bilgisi (web/ios/android)
- Cihaz bilgileri

#### sms_credits
- SMS kredileri
- Kullanım istatistikleri

### API Endpoints

```
GET    /notifications                   # Bildirim listesi
GET    /notifications/settings          # Bildirim ayarları
POST   /notifications/settings          # Ayarları güncelle
POST   /notifications/{id}/read         # Okundu işaretle
POST   /notifications/read-all          # Tümünü okundu işaretle
DELETE /notifications/{id}              # Bildirimi sil
POST   /notifications/clear             # Tüm bildirimleri temizle
POST   /notifications/push/register     # Push token kaydet
POST   /notifications/push/unregister   # Push token sil
POST   /notifications/test              # Test bildirimi gönder
GET    /notifications/unread-count      # Okunmamış sayısı (AJAX)
GET    /notifications/recent            # Son bildirimler (AJAX)
```

### Bildirim Ayarları

```php
// Kullanıcı ayarlarını güncelleme
$notificationService->updateSettings($user, [
    'email_enabled' => true,
    'email_messages' => true,
    'sms_enabled' => true,
    'sms_phone' => '+905551234567',
    'push_enabled' => true,
    'quiet_hours_enabled' => true,
    'quiet_hours_start' => '22:00',
    'quiet_hours_end' => '08:00',
    'digest_frequency' => 'weekly',
]);
```

---

## 🔧 Konfigürasyon

### .env Ayarları

```env
# SMS Configuration
SMS_PROVIDER=twilio
SMS_FROM=+15551234567
TWILIO_SID=your_sid
TWILIO_TOKEN=your_token
TWILIO_FROM=+15551234567

# Push Notifications
VAPID_PUBLIC_KEY=your_public_key
VAPID_PRIVATE_KEY=your_private_key
FCM_SERVER_KEY=your_fcm_key

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue (for notifications)
QUEUE_CONNECTION=redis
```

### Cron Jobs

```cron
# Process notification queue every minute
* * * * * cd /path-to-project && php artisan queue:work --stop-when-empty

# Send digest notifications daily at 9 AM
0 9 * * * cd /path-to-project && php artisan notifications:send-digests

# Clean old notifications monthly
0 0 1 * * cd /path-to-project && php artisan notifications:cleanup
```

---

## 🎨 Frontend Entegrasyonu

### Bildirim Badge
```html
<div class="notification-icon">
    <i class="fas fa-bell"></i>
    <span class="badge" id="notification-count">0</span>
</div>

<script>
// Okunmamış bildirim sayısını güncelle
setInterval(function() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            document.getElementById('notification-count').textContent = data.count;
        });
}, 30000); // Her 30 saniyede
</script>
```

### Mesaj Gönderme Formu
```html
<form id="message-form" action="/messages/{{ $thread->id }}/send" method="POST">
    @csrf
    <textarea name="message" required></textarea>
    <button type="submit">Gönder</button>
</form>

<script>
// AJAX ile gönder
document.getElementById('message-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mesajı ekle
            appendMessage(data.message);
            this.reset();
        }
    });
});
</script>
```

### Push Notification İzni
```javascript
// Push notification izni iste
Notification.requestPermission().then(function(permission) {
    if (permission === 'granted') {
        // Subscribe to push notifications
        subscribeToPush();
    }
});

function subscribeToPush() {
    navigator.serviceWorker.ready.then(function(registration) {
        return registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey)
        });
    }).then(function(subscription) {
        // Send subscription to server
        return fetch('/notifications/push/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                token: subscription.endpoint,
                platform: 'web'
            })
        });
    });
}
```

---

## 🚀 Performans Optimizasyonu

### Queue Worker
```bash
# Birden fazla worker çalıştır
php artisan queue:work --queue=high,default,low --tries=3

# Supervisor config
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/worker.log
```

### Cache Kullanımı
```php
// Bildirim ayarlarını cache'le
Cache::remember("user_notification_settings_{$userId}", 3600, function() use ($userId) {
    return DB::table('notification_settings')->where('user_id', $userId)->first();
});
```

### Database İndeksleme
```sql
-- Performans için önemli indeksler
CREATE INDEX idx_tickets_user_status ON tickets(user_id, status);
CREATE INDEX idx_messages_thread_created ON messages(thread_id, created_at);
CREATE INDEX idx_notifications_user_read ON notifications(notifiable_id, read_at);
```

---

## 🐛 Sorun Giderme

### Bildirimler gönderilmiyor
1. Queue worker'ın çalıştığından emin olun
2. `.env` dosyasındaki mail/SMS ayarlarını kontrol edin
3. `failed_jobs` tablosunu kontrol edin

### Mesajlar görünmüyor
1. Kullanıcının thread'e katılımcı olduğundan emin olun
2. Thread'in aktif olduğunu kontrol edin
3. Engellenme durumunu kontrol edin

### Push notification çalışmıyor
1. HTTPS kullandığınızdan emin olun
2. Service worker'ın kayıtlı olduğunu kontrol edin
3. VAPID key'lerinin doğru olduğunu kontrol edin

---

## 📝 Notlar

1. **Güvenlik**: Tüm kullanıcı girdileri validate edilir ve sanitize edilir
2. **Rate Limiting**: API endpoint'leri rate limiting ile korunur
3. **Monitoring**: Tüm önemli işlemler activity log'a kaydedilir
4. **Scalability**: Queue sistemi ile yüksek yük altında çalışabilir
5. **Testing**: Unit ve feature test'ler yazılmalıdır

---

## 📞 Destek

Sorunlar için GitHub Issues kullanın veya dokümantasyonu kontrol edin.

---

**Version:** 1.0.0  
**Laravel Version:** 12.x  
**PHP Version:** 8.3+  
**Yazar:** Laravel Multi-Vendor Team
