<?php
/**
 * Laravel Route Checker
 * Bu script route'ları kontrol eder ve activity-log route'larının durumunu gösterir
 */

// Laravel bootstrap
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "================================\n";
echo "Laravel Route Kontrolü\n";
echo "================================\n\n";

// Tüm route'ları al
$routes = Route::getRoutes();

// Activity log route'larını filtrele
$activityRoutes = [];
foreach ($routes as $route) {
    $name = $route->getName();
    if ($name && str_contains($name, 'activity-log')) {
        $activityRoutes[] = [
            'name' => $name,
            'uri' => $route->uri(),
            'methods' => implode('|', $route->methods()),
            'action' => $route->getActionName()
        ];
    }
}

if (empty($activityRoutes)) {
    echo "❌ Activity Log route'ları bulunamadı!\n\n";
} else {
    echo "✅ Activity Log Route'ları:\n";
    echo "----------------------------\n";
    
    foreach ($activityRoutes as $route) {
        echo "İsim: " . $route['name'] . "\n";
        echo "URI: " . $route['uri'] . "\n";
        echo "Method: " . $route['methods'] . "\n";
        echo "Action: " . $route['action'] . "\n";
        echo "----------------------------\n";
    }
}

// Spesifik route'ları kontrol et
$requiredRoutes = [
    'admin.activity-log.index',
    'admin.activity-log.export',
    'admin.activity-log.clear',
    'admin.activity-log.show'
];

echo "\n✅ Route Durumu:\n";
echo "----------------------------\n";

foreach ($requiredRoutes as $routeName) {
    if (Route::has($routeName)) {
        $route = Route::getRoutes()->getByName($routeName);
        echo "✅ $routeName -> " . $route->uri() . "\n";
    } else {
        echo "❌ $routeName -> BULUNAMADI!\n";
    }
}

// Controller'ı kontrol et
echo "\n✅ Controller Kontrolü:\n";
echo "----------------------------\n";

$controllerClass = 'App\Modules\ActivityLog\Controllers\ActivityLogController';
if (class_exists($controllerClass)) {
    echo "✅ Controller sınıfı mevcut\n";
    
    $methods = ['index', 'show', 'export', 'clear'];
    foreach ($methods as $method) {
        if (method_exists($controllerClass, $method)) {
            echo "  ✅ $method() metodu mevcut\n";
        } else {
            echo "  ❌ $method() metodu BULUNAMADI!\n";
        }
    }
} else {
    echo "❌ Controller sınıfı bulunamadı: $controllerClass\n";
}

// View'leri kontrol et
echo "\n✅ View Kontrolü:\n";
echo "----------------------------\n";

$viewPath = resource_path('views/admin/activity-log');
if (is_dir($viewPath)) {
    echo "✅ View klasörü mevcut: $viewPath\n";
    
    $views = ['index.blade.php', 'show.blade.php'];
    foreach ($views as $view) {
        $fullPath = $viewPath . '/' . $view;
        if (file_exists($fullPath)) {
            echo "  ✅ $view mevcut\n";
        } else {
            echo "  ❌ $view BULUNAMADI!\n";
        }
    }
} else {
    echo "❌ View klasörü bulunamadı: $viewPath\n";
}

echo "\n================================\n";
echo "Kontrol Tamamlandı!\n";
echo "================================\n";
