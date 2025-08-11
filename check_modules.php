#!/usr/bin/env php
<?php

echo "================================\n";
echo "Laravel MV - Modül Kontrol Script\n";
echo "================================\n\n";

$modulesPath = __DIR__ . '/app/Modules';
$viewsPath = __DIR__ . '/resources/views';

// Tüm modülleri al
$modules = array_filter(glob($modulesPath . '/*'), 'is_dir');
$modules = array_map('basename', $modules);

$report = [];
$missingCount = 0;

echo "Toplam " . count($modules) . " modül bulundu.\n\n";

foreach ($modules as $module) {
    $modulePath = $modulesPath . '/' . $module;
    $moduleReport = [
        'name' => $module,
        'controllers' => false,
        'models' => false,
        'requests' => false,
        'services' => false,
        'views' => false,
        'missing' => []
    ];
    
    // Controllers kontrolü
    if (is_dir($modulePath . '/Controllers')) {
        $controllers = glob($modulePath . '/Controllers/*.php');
        $moduleReport['controllers'] = count($controllers) > 0;
        if (!$moduleReport['controllers']) {
            $moduleReport['missing'][] = 'Controller dosyası yok';
            $missingCount++;
        }
    } else {
        $moduleReport['missing'][] = 'Controllers klasörü yok';
        $missingCount++;
    }
    
    // Models kontrolü
    if (is_dir($modulePath . '/Models')) {
        $models = glob($modulePath . '/Models/*.php');
        $moduleReport['models'] = count($models) > 0;
        if (!$moduleReport['models']) {
            $moduleReport['missing'][] = 'Model dosyası yok';
            $missingCount++;
        }
    } else {
        $moduleReport['missing'][] = 'Models klasörü yok';
        $missingCount++;
    }
    
    // Requests kontrolü
    if (is_dir($modulePath . '/Requests')) {
        $requests = glob($modulePath . '/Requests/*.php');
        $moduleReport['requests'] = count($requests) > 0;
    } else {
        $moduleReport['requests'] = false;
        $moduleReport['missing'][] = 'Requests klasörü yok';
        $missingCount++;
    }
    
    // Services kontrolü
    if (is_dir($modulePath . '/Services')) {
        $services = glob($modulePath . '/Services/*.php');
        $moduleReport['services'] = count($services) > 0;
    } else {
        $moduleReport['services'] = false;
        $moduleReport['missing'][] = 'Services klasörü yok';
        $missingCount++;
    }
    
    // Views kontrolü
    $viewFolder = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $module));
    $viewPath = $viewsPath . '/' . $viewFolder;
    
    if (is_dir($viewPath)) {
        $viewFiles = glob($viewPath . '/*.blade.php');
        $hasIndex = file_exists($viewPath . '/index.blade.php');
        $hasCreate = file_exists($viewPath . '/create.blade.php');
        $hasEdit = file_exists($viewPath . '/edit.blade.php');
        $hasShow = file_exists($viewPath . '/show.blade.php');
        
        $moduleReport['views'] = $hasIndex;
        
        if (!$hasIndex) $moduleReport['missing'][] = 'index.blade.php yok';
        if (!$hasCreate && $module !== 'ActivityLog' && $module !== 'Settings') {
            $moduleReport['missing'][] = 'create.blade.php yok';
            $missingCount++;
        }
        if (!$hasEdit && $module !== 'ActivityLog') {
            $moduleReport['missing'][] = 'edit.blade.php yok';
            $missingCount++;
        }
        if (!$hasShow && $module !== 'Settings') {
            $moduleReport['missing'][] = 'show.blade.php yok';
            $missingCount++;
        }
    } else {
        $moduleReport['views'] = false;
        $moduleReport['missing'][] = 'Views klasörü yok: ' . $viewFolder;
        $missingCount++;
    }
    
    $report[] = $moduleReport;
}

// Raporu yazdır
echo "MODÜL DURUM RAPORU\n";
echo "==================\n\n";

foreach ($report as $module) {
    $status = empty($module['missing']) ? '✅' : '⚠️';
    echo $status . " " . $module['name'] . "\n";
    
    if (!empty($module['missing'])) {
        foreach ($module['missing'] as $missing) {
            echo "   ❌ " . $missing . "\n";
        }
    }
    echo "\n";
}

echo "==================\n";
echo "ÖZET: " . $missingCount . " eksik bulundu\n";

// Eksikleri JSON olarak kaydet
file_put_contents(__DIR__ . '/module_report.json', json_encode($report, JSON_PRETTY_PRINT));
echo "\nDetaylı rapor module_report.json dosyasına kaydedildi.\n";
