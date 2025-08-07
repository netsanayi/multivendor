# Laravel Multi-Vendor Marketplace - Final Setup Instructions

## 🎉 Tebrikler! Proje Yapısı Hazır

Proje dosyaları başarıyla oluşturuldu. Şimdi kurulumu tamamlamak için aşağıdaki adımları takip edin:

## 📋 Kurulum Adımları

### 1. Terminal'i açın ve proje klasörüne gidin:
```bash
cd C:\Users\Murat\Projects\laravelmv
```

### 2. Composer bağımlılıklarını yükleyin:
```bash
composer install
```

### 3. NPM bağımlılıklarını yükleyin:
```bash
npm install
```

### 4. .env dosyasını oluşturun:
```bash
copy .env.example .env
```

### 5. Uygulama anahtarını oluşturun:
```bash
php artisan key:generate
```

### 6. PostgreSQL veritabanını oluşturun:

PostgreSQL'de `laravelmv` adında bir veritabanı oluşturun veya pgAdmin kullanarak oluşturun.

### 7. Veritabanı migration'larını çalıştırın:
```bash
php artisan migrate
```

### 8. Örnek verileri yükleyin:
```bash
php artisan db:seed
```

### 9. Storage linkini oluşturun:
```bash
php artisan storage:link
```

### 10. Gerekli klasör izinlerini ayarlayın:
```bash
# Windows'ta genellikle gerekli değil, ancak Linux/Mac'te:
# chmod -R 775 storage
# chmod -R 775 bootstrap/cache
```

### 11. Frontend varlıklarını derleyin:
```bash
npm run build
```

### 12. Uygulamayı başlatın:
```bash
php artisan serve
```

## 🔑 Giriş Bilgileri

Seed dosyaları ile oluşturulan kullanıcılar:

### Admin Kullanıcı:
- Email: admin@example.com
- Şifre: password

### Satıcı Kullanıcılar:
- Email: vendor1@example.com, vendor2@example.com, vendor3@example.com
- Şifre: password

### Müşteri Kullanıcılar:
- Email: customer1@example.com ... customer5@example.com
- Şifre: password

### Editor Kullanıcı:
- Email: editor@example.com
- Şifre: password

## 🌐 Erişim Adresleri

- Ana Sayfa: http://localhost:8000
- Admin Panel: http://localhost:8000/admin
- Vendor Panel: http://localhost:8000/vendor
- Kullanıcı Paneli: http://localhost:8000/dashboard

## 🛠️ Geliştirme Modu

Geliştirme yaparken Vite'i çalıştırın:
```bash
npm run dev
```

## 🐛 Sorun Giderme

### Composer hatası alıyorsanız:
```bash
composer update
composer dump-autoload
```

### NPM hatası alıyorsanız:
```bash
npm cache clean --force
npm install
```

### Migration hatası alıyorsanız:
- PostgreSQL servisinin çalıştığından emin olun
- .env dosyasındaki veritabanı bilgilerini kontrol edin
- Veritabanının oluşturulduğundan emin olun

### Storage permission hatası:
```bash
php artisan storage:link --force
```

## 📚 Eksik Modüller

Aşağıdaki modüller henüz tamamlanmamıştır ve geliştirmeniz gerekebilir:
- Sipariş Yönetimi (Orders)
- Sepet (Cart)
- Ödeme (Payment)
- Kargo (Shipping)
- Kupon (Coupons)
- İnceleme ve Puanlama (Reviews)
- Bildirimler (Notifications)
- Raporlama (Reports)

## 🚀 Sonraki Adımlar

1. View dosyalarını tamamlayın (vendor-products, brands, users vb.)
2. Controller'ları tamamlayın
3. API endpoint'lerini geliştirin
4. Frontend tasarımını özelleştirin
5. Test yazın
6. Deployment hazırlıkları yapın

## 📞 Destek

Herhangi bir sorun yaşarsanız, lütfen projenin README.md dosyasını kontrol edin veya issue açın.

Başarılar! 🎊
