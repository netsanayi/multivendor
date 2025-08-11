@echo off
echo Laravel Projesini Yenileme...
echo.

echo [1/6] Composer autoload yenileniyor...
composer dump-autoload

echo.
echo [2/6] Route cache temizleniyor...
php artisan route:clear

echo.
echo [3/6] Config cache temizleniyor...
php artisan config:clear

echo.
echo [4/6] View cache temizleniyor...
php artisan view:clear

echo.
echo [5/6] Cache temizleniyor...
php artisan cache:clear

echo.
echo [6/6] Route listesi olusturuluyor...
php artisan route:list --name=admin.activity-log

echo.
echo ===============================================
echo Islem tamamlandi!
echo ===============================================
echo.
echo Activity Log sayfasina gitmek icin:
echo http://localhost/laravelmv/admin/activity-log
echo.
pause
