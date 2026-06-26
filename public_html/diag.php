<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Server Diagnostics</h1>";
echo "<p><b>PHP Version:</b> " . phpversion() . "</p>";
echo "<p><b>Document Root:</b> " . __DIR__ . "</p>";

// Check key files
$files = ['vendor/autoload.php', 'bootstrap/app.php', '.env', 'artisan', 'storage', 'bootstrap/cache'];
foreach ($files as $f) {
    $path = __DIR__ . '/' . $f;
    $exists = file_exists($path);
    $writable = $exists ? is_writable($path) : false;
    echo "<p><b>$f:</b> " . ($exists ? 'EXISTS' : 'MISSING') . ($exists ? ($writable ? ' (writable)' : ' (NOT writable)') : '') . "</p>";
}

// Try autoload
echo "<h2>Testing Autoload</h2>";
try {
    require __DIR__ . '/vendor/autoload.php';
    echo "<p style='color:green'>Autoload OK</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>Autoload Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// Try bootstrap
echo "<h2>Testing Bootstrap</h2>";
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "<p style='color:green'>Bootstrap OK</p>";
} catch (Throwable $e) {
    echo "<p style='color:red'>Bootstrap Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
}

// Check error log
$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $log = file_get_contents($logPath);
    $lines = explode("\n", trim($log));
    $lastLines = array_slice($lines, -30);
    echo "<h2>Last 30 log lines:</h2>";
    echo "<pre style='background:#f5f5f5; padding:10px; font-size:11px;'>";
    echo htmlspecialchars(implode("\n", $lastLines));
    echo "</pre>";
} else {
    echo "<p><b>laravel.log:</b> NOT FOUND at $logPath</p>";
    // Check PHP error log
    $phpLog = '/home/lakeviex/error_log';
    if (file_exists($phpLog)) {
        $log = file_get_contents($phpLog);
        $lines = explode("\n", trim($log));
        $lastLines = array_slice($lines, -10);
        echo "<h2>PHP error_log (last 10 lines):</h2>";
        echo "<pre style='background:#f5f5f5; padding:10px; font-size:11px;'>";
        echo htmlspecialchars(implode("\n", $lastLines));
        echo "</pre>";
    }
}
