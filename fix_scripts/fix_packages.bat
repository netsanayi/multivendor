@echo off
echo ========================================
echo Laravel Package Fix Script
echo ========================================
echo.

cd C:\Users\Murat\Projects\laravelmv

echo [1/8] Composer cache temizleniyor...
call composer clear-cache

echo.
echo [2/8] Autoload dump yapiliyor...
call composer dump-autoload

echo.
echo [3/8] Eksik paketler kontrol ediliyor ve yukleniyor...
call composer install --no-scripts

echo.
echo [4/8] Vendor klasoru optimize ediliyor...
call composer dump-autoload -o

echo.
echo [5/8] Laravel cache temizleniyor...
call php artisan cache:clear
call php artisan config:clear
call php artisan route:clear
call php artisan view:clear

echo.
echo [6/8] APP_KEY olusturuluyor...
call php artisan key:generate

echo.
echo [7/8] Config cache yenileniyor...
call php artisan config:cache

echo.
echo [8/8] Paket discovery calistiriliyor...
call php artisan package:discover

echo.
echo ========================================
echo ISLEM TAMAMLANDI!
echo ========================================
echo.
echo Simdi sunucu baslatilabilir:
echo php artisan serve
echo.
pause
