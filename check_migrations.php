<?php
// Migration durumunu kontrol et
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== TABLOLAR ===\n";
if (Schema::hasTable('users')) {
    echo "✓ users tablosu mevcut\n";
    $userColumns = Schema::getColumnListing('users');
    echo "  Kolonlar: " . implode(', ', $userColumns) . "\n";
}

if (Schema::hasTable('migrations')) {
    echo "\n=== ÇALIŞTIRILMIŞ MIGRATION'LAR ===\n";
    $migrations = DB::table('migrations')->orderBy('batch')->get();
    foreach ($migrations as $migration) {
        echo "Batch {$migration->batch}: {$migration->migration}\n";
    }
}

echo "\n=== DOSYA SİSTEMİNDEKİ MIGRATION'LAR ===\n";
$files = scandir('database/migrations');
foreach ($files as $file) {
    if (str_ends_with($file, '.php')) {
        echo $file . "\n";
    }
}
