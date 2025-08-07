@echo off
echo ========================================
echo Vendor Dashboard ve Wishlist Modulleri Kurulumu
echo ========================================
echo.

cd C:\Users\Murat\Projects\laravelmv

echo [1/6] Migration'lar calistiriliyor...
call php artisan migrate

echo.
echo [2/6] Cache temizleniyor...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear

echo.
echo [3/6] Config cache yenileniyor...
call php artisan config:cache

echo.
echo [4/6] Route cache yenileniyor...
call php artisan route:cache

echo.
echo [5/6] Composer autoload yenileniyor...
call composer dump-autoload

echo.
echo [6/6] Storage link olusturuluyor (eger yoksa)...
call php artisan storage:link

echo.
echo ========================================
echo KURULUM TAMAMLANDI!
echo ========================================
echo.
echo Vendor Dashboard'a erisim icin:
echo - Bir kullanici olusturun ve 'vendor' rolu atayin
echo - http://localhost:8000/vendor adresine gidin
echo.
echo Wishlist'e erisim icin:
echo - Giris yapin ve http://localhost:8000/wishlist adresine gidin
echo - Urun sayfalarinda Livewire component'ini kullanin:
echo   @livewire('wishlist-button', ['productId' => $product->id])
echo.
pause
