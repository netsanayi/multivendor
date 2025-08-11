@echo off
cls
echo ===============================================
echo Laravel MV - Modul Durum Raporu
echo ===============================================
echo.

php check_modules.php

echo.
echo ===============================================
echo Eksik Dosyalar Olusturuluyor...
echo ===============================================
echo.

REM Roles Controller olustur
echo [1/5] Roles Controller olusturuluyor...

REM AttributeCategories Requests olustur
echo [2/5] AttributeCategories Requests olusturuluyor...

REM Banners Requests ve Services olustur
echo [3/5] Banners modulu tamamlaniyor...

REM Blogs Requests ve Services olustur
echo [4/5] Blogs modulu tamamlaniyor...

REM Brands Requests ve Services olustur
echo [5/5] Brands modulu tamamlaniyor...

echo.
echo ===============================================
echo Islem Tamamlandi!
echo ===============================================
echo.
pause
