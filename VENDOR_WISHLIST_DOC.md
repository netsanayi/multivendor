# 📦 Vendor Dashboard & Wishlist Modülleri Dokümantasyonu

## 🎯 Genel Bakış

Bu dokümantasyon, Laravel Multi-Vendor Marketplace uygulamasına eklenen **Vendor Dashboard** ve **Wishlist** modüllerinin kullanımını açıklar.

---

## 🏪 Vendor Dashboard Modülü

### Özellikler
- 📊 **Dashboard**: Satıcı istatistikleri ve grafikler
- 💰 **Kazanç Yönetimi**: Detaylı kazanç takibi
- 💳 **Ödeme Talepleri**: Otomatik ödeme sistemi
- 📦 **Ürün Yönetimi**: Vendor ürünlerini yönetme
- 📈 **Analitik**: Satış ve performans analizleri
- ⚙️ **Ayarlar**: Mağaza ayarları ve banka bilgileri

### Kurulum

1. **Migration'ları çalıştırın:**
```bash
php artisan migrate
```

2. **Test verisi oluşturun:**
```bash
php artisan db:seed --class=VendorSeeder
```

3. **Vendor hesabıyla giriş yapın:**
- Email: vendor1@example.com
- Şifre: password

### Kullanım

#### Dashboard Erişimi
```
http://localhost:8000/vendor
```

#### Komisyon Hesaplama
```php
use App\Modules\VendorDashboard\Services\VendorDashboardService;

$service = new VendorDashboardService();
$commission = $service->calculateCommission($vendorId, $amount);
// Returns: ['gross_amount', 'commission_amount', 'net_amount']
```

#### Ödeme Talebi Oluşturma
```php
// Controller'da
public function requestPayout(Request $request)
{
    // Validation...
    $payout = VendorPayout::create([
        'vendor_id' => auth()->id(),
        'amount' => $request->amount,
        'payment_method' => $request->payment_method,
        // ...
    ]);
}
```

### Veritabanı Tabloları

#### vendor_commissions
- `id`: Primary key
- `vendor_id`: Satıcı ID
- `commission_rate`: Komisyon oranı (%)
- `commission_type`: percentage|fixed|tiered
- `is_active`: Aktif/Pasif durumu

#### vendor_earnings
- `id`: Primary key
- `vendor_id`: Satıcı ID
- `order_id`: Sipariş ID (opsiyonel)
- `gross_amount`: Brüt tutar
- `commission_amount`: Komisyon tutarı
- `net_amount`: Net tutar
- `status`: pending|approved|paid|cancelled|refunded

#### vendor_payouts
- `id`: Primary key
- `vendor_id`: Satıcı ID
- `payout_number`: Ödeme numarası
- `amount`: Ödeme tutarı
- `status`: pending|processing|completed|failed|cancelled
- `payment_method`: bank_transfer|paypal|stripe|manual

---

## ❤️ Wishlist (Favoriler) Modülü

### Özellikler
- ➕ **Ürün Ekleme/Çıkarma**: Ajax ile hızlı işlem
- ⭐ **Öncelik Belirleme**: 0-10 arası öncelik seviyesi
- 📝 **Not Ekleme**: Her ürün için özel notlar
- 🔔 **İndirim Bildirimi**: Fiyat düşüşü bildirimleri
- 🔗 **Paylaşım**: Favori listesini paylaşma
- 🛒 **Toplu Sepete Ekleme**: Tüm ürünleri sepete ekleme
- 📊 **İstatistikler**: Toplam değer, tasarruf miktarı

### Kurulum

1. **Migration'ları çalıştırın:**
```bash
php artisan migrate
```

2. **Livewire component'ini publish edin:**
```bash
php artisan livewire:publish --config
```

### Kullanım

#### Wishlist Sayfası
```
http://localhost:8000/wishlist
```

#### Livewire Component Kullanımı

Ürün sayfalarında wishlist butonu eklemek için:

```blade
{{-- Basit kullanım --}}
@livewire('wishlist-button', ['productId' => $product->id])

{{-- Özelleştirilmiş kullanım --}}
@livewire('wishlist-button', [
    'productId' => $product->id,
    'showText' => false,  // Sadece ikon göster
    'size' => 'sm'        // Küçük buton
])
```

#### JavaScript API

```javascript
// Wishlist'e ürün ekle/çıkar
toggleWishlist(productId);

// Wishlist'ten ürün çıkar
removeFromWishlist(wishlistId);

// Öncelik güncelle
updatePriority(wishlistId, priority);

// Tüm listeyi temizle
clearWishlist();

// Tümünü sepete ekle
addAllToCart();

// Listeyi paylaş
shareWishlist();
```

#### PHP API

```php
use App\Modules\Wishlists\Models\Wishlist;

// Ürün ekle
Wishlist::addProduct($userId, $productId, [
    'priority' => 5,
    'notes' => 'Doğum günü hediyesi'
]);

// Ürün çıkar
Wishlist::removeProduct($userId, $productId);

// Toggle (ekle/çıkar)
$result = Wishlist::toggleProduct($userId, $productId);
// Returns: ['action' => 'added'|'removed', 'wishlist' => Model|null]

// Kontrol et
$exists = Wishlist::userHasProduct($userId, $productId);

// Kullanıcı özeti
$summary = Wishlist::getUserSummary($userId);
// Returns: ['total_items', 'total_value', 'on_sale_count', 'total_savings']
```

### Service Metodları

```php
use App\Modules\Wishlists\Services\WishlistService;

$service = new WishlistService();

// Fiyat düşüşlerini kontrol et ve bildir
$service->checkPriceDrops();

// Önerilen ürünleri getir
$products = $service->getRecommendedProducts($userId, $limit);

// Wishlist'i dışa aktar
$export = $service->exportWishlist($userId, 'json'); // json|csv|pdf

// İstatistikleri getir
$stats = $service->getUserStatistics($userId);

// Paylaşım token'ı oluştur
$token = $service->generateShareToken($userId);
```

### Veritabanı Tablosu

#### wishlists
- `id`: Primary key
- `user_id`: Kullanıcı ID
- `product_id`: Ürün ID
- `priority`: Öncelik (0-10)
- `notes`: Kullanıcı notları
- `added_at`: Eklenme tarihi
- `notified_at`: Bildirim tarihi
- `price_when_added`: Eklendiğindeki fiyat
- `notify_on_sale`: İndirim bildirimi aktif/pasif

---

## 🔧 Yapılandırma

### Vendor Dashboard Ayarları

```php
// config/vendor.php (oluşturmanız gerekiyor)
return [
    'commission' => [
        'default_rate' => 10, // Varsayılan komisyon oranı (%)
        'min_payout' => 50,   // Minimum ödeme tutarı
    ],
    'payouts' => [
        'auto_approve' => false, // Otomatik onay
        'processing_days' => 7,  // İşlem süresi
    ],
];
```

### Wishlist Ayarları

```php
// config/wishlist.php (oluşturmanız gerekiyor)
return [
    'max_items' => 100,        // Maksimum ürün sayısı
    'share_token_days' => 30,  // Paylaşım linki geçerlilik süresi
    'notify_on_sale' => true,  // Varsayılan bildirim ayarı
];
```

---

## 🎨 Özelleştirme

### Vendor Dashboard Tema

`resources/views/layouts/vendor.blade.php` dosyasını düzenleyerek tema renklerini değiştirebilirsiniz.

### Wishlist Görünümü

`resources/views/wishlists/index.blade.php` dosyasını düzenleyerek wishlist sayfasının görünümünü özelleştirebilirsiniz.

### Livewire Component

`resources/views/livewire/wishlist-button.blade.php` dosyasını düzenleyerek buton stilini değiştirebilirsiniz.

---

## 🐛 Sorun Giderme

### "Class not found" hatası
```bash
composer dump-autoload
php artisan config:clear
```

### Migration hatası
```bash
php artisan migrate:fresh --seed
```

### Livewire component çalışmıyor
```bash
php artisan livewire:discover
php artisan view:clear
```

### Route bulunamadı hatası
```bash
php artisan route:clear
php artisan route:cache
```

---

## 📝 Notlar

1. **Güvenlik**: Tüm vendor işlemleri middleware ile korunmaktadır
2. **Performance**: Wishlist sorguları optimize edilmiştir
3. **Cache**: Paylaşım linkleri Redis/Cache'de saklanır
4. **Activity Log**: Tüm önemli işlemler loglanır
5. **Notifications**: Email bildirimleri için Mail konfigürasyonu gerekli

---

## 🚀 Gelecek Geliştirmeler

- [ ] Otomatik ödeme işleme (PayPal, Stripe entegrasyonu)
- [ ] Gelişmiş raporlama ve Excel export
- [ ] Wishlist karşılaştırma özelliği
- [ ] Fiyat takibi ve grafik gösterimi
- [ ] Vendor badge sistemi
- [ ] API endpoints
- [ ] Mobile app desteği

---

## 📞 Destek

Sorunlar için GitHub Issues kullanın veya dokümantasyonu kontrol edin.

---

**Version:** 1.0.0  
**Laravel Version:** 12.x  
**PHP Version:** 8.3+  
**Yazar:** Laravel Multi-Vendor Team
