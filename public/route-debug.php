<?php
/**
 * Route Debug Helper
 * Bu dosyayı tarayıcıdan çalıştırarak route'ları kontrol edebilirsiniz
 * http://localhost:8000/route-debug.php
 */

// Laravel bootstrap
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Route Debug</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .success {
            color: #4CAF50;
            font-weight: bold;
        }
        .error {
            color: #f44336;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .section {
            margin: 30px 0;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-get { background: #4CAF50; color: white; }
        .badge-post { background: #2196F3; color: white; }
        .badge-put { background: #FF9800; color: white; }
        .badge-patch { background: #9C27B0; color: white; }
        .badge-delete { background: #f44336; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Laravel Route Debug</h1>
        
        <div class="section">
            <h2>Activity Log Routes</h2>
            <?php
            $activityRoutes = [];
            foreach (Route::getRoutes() as $route) {
                $name = $route->getName();
                if ($name && str_contains($name, 'activity-log')) {
                    $activityRoutes[] = $route;
                }
            }
            
            if (empty($activityRoutes)):
            ?>
                <p class="error">❌ Activity Log route'ları bulunamadı!</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Route Adı</th>
                            <th>URI</th>
                            <th>Method</th>
                            <th>Controller</th>
                            <th>Durum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activityRoutes as $route): ?>
                            <tr>
                                <td><span class="code"><?= $route->getName() ?></span></td>
                                <td><span class="code"><?= $route->uri() ?></span></td>
                                <td>
                                    <?php foreach ($route->methods() as $method): ?>
                                        <span class="badge badge-<?= strtolower($method) ?>"><?= $method ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><small><?= $route->getActionName() ?></small></td>
                                <td><span class="success">✅ Aktif</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Route Kontrolü</h2>
            <?php
            $requiredRoutes = [
                'admin.activity-log.index' => 'Liste Sayfası',
                'admin.activity-log.export' => 'Export İşlemi',
                'admin.activity-log.clear' => 'Temizleme İşlemi',
                'admin.activity-log.show' => 'Detay Sayfası'
            ];
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Route Adı</th>
                        <th>Açıklama</th>
                        <th>URL</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requiredRoutes as $routeName => $description): ?>
                        <tr>
                            <td><span class="code"><?= $routeName ?></span></td>
                            <td><?= $description ?></td>
                            <td>
                                <?php if (Route::has($routeName)): ?>
                                    <?php $route = Route::getRoutes()->getByName($routeName); ?>
                                    <span class="code"><?= $route->uri() ?></span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (Route::has($routeName)): ?>
                                    <span class="success">✅ Mevcut</span>
                                <?php else: ?>
                                    <span class="error">❌ Bulunamadı</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Controller Kontrolü</h2>
            <?php
            $controllerClass = 'App\Modules\ActivityLog\Controllers\ActivityLogController';
            $controllerExists = class_exists($controllerClass);
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Controller</th>
                        <th>Method</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">
                            <strong><?= $controllerClass ?></strong>
                            <?php if ($controllerExists): ?>
                                <span class="success">✅ Sınıf mevcut</span>
                            <?php else: ?>
                                <span class="error">❌ Sınıf bulunamadı</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php if ($controllerExists): ?>
                        <?php
                        $methods = ['index', 'show', 'export', 'clear'];
                        foreach ($methods as $method):
                        ?>
                            <tr>
                                <td></td>
                                <td><span class="code"><?= $method ?>()</span></td>
                                <td>
                                    <?php if (method_exists($controllerClass, $method)): ?>
                                        <span class="success">✅ Mevcut</span>
                                    <?php else: ?>
                                        <span class="error">❌ Bulunamadı</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Çözüm Önerileri</h2>
            <ol>
                <li>Terminal'de şu komutu çalıştırın: <span class="code">php artisan route:clear</span></li>
                <li>Composer autoload'ı yenileyin: <span class="code">composer dump-autoload</span></li>
                <li>Laravel optimize komutunu çalıştırın: <span class="code">php artisan optimize:clear</span></li>
                <li>Sunucuyu yeniden başlatın: <span class="code">php artisan serve</span></li>
            </ol>
        </div>

        <div class="section">
            <h2>Test Linkleri</h2>
            <ul>
                <li><a href="/admin/activity-log" target="_blank">Activity Log Index</a></li>
                <li><a href="/admin/activity-log/export" target="_blank">Activity Log Export (GET)</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
