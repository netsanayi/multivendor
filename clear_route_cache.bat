@echo off
echo Laravel Route Cache Temizleniyor...
echo.

echo Route cache temizleniyor...
php artisan route:clear

echo Config cache temizleniyor...
php artisan config:clear

echo Cache temizleniyor...
php artisan cache:clear

echo View cache temizleniyor...
php artisan view:clear

echo.
echo Cache basariyla temizlendi!
echo.

echo Activity Log Route'lari kontrol ediliyor:
php artisan route:list | findstr "activity-log"

echo.
echo Islem tamamlandi.
pause
