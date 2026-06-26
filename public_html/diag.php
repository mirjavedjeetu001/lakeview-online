<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Server Diagnostics</h1>";
echo "<p><b>PHP Version:</b> " . phpversion() . "</p>";
echo "<p><b>Server:</b> " . php_sapi_name() . "</p>";

// Check if vendor autoload exists
$autoloadPath = __DIR__ . '/vendor/autoload.php';
echo "<p><b>vendor/autoload.php:</b> " . (file_exists($autoloadPath) ? 'EXISTS' : 'MISSING') . "</p>";

// Try to load autoload
if (file_exists($autoloadPath)) {
    try {
        require $autoloadPath;
        echo "<p style='color:green'>Autoload loaded successfully</p>";
    } catch (Throwable $e) {
        echo "<p style='color:red'>Autoload error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

// Check bootstrap
$bootstrapPath = __DIR__ . '/bootstrap/app.php';
echo "<p><b>bootstrap/app.php:</b> " . (file_exists($bootstrapPath) ? 'EXISTS' : 'MISSING') . "</p>";

// Check .env
$envPath = __DIR__ . '/.env';
echo "<p><b>.env:</b> " . (file_exists($envPath) ? 'EXISTS' : 'MISSING') . "</p>";

// Check storage permissions
echo "<p><b>storage/:</b> " . (is_writable(__DIR__ . '/storage') ? 'WRITABLE' : 'NOT WRITABLE') . "</p>";
echo "<p><b>bootstrap/cache/:</b> " . (is_writable(__DIR__ . '/bootstrap/cache') ? 'WRITABLE' : 'NOT WRITABLE') . "</p>";

// Try to bootstrap Laravel
if (file_exists($bootstrapPath)) {
    try {
        $app = require_once $bootstrapPath;
        echo "<p style='color:green'>Laravel bootstrapped successfully</p>";
    } catch (Throwable $e) {
        echo "<p style='color:red'>Bootstrap error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p style='color:red'>File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
        echo "<pre style='color:red; background:#fee; padding:10px;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}

// Check error log
$logPath = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logPath)) {
    $log = file_get_contents($logPath);
    $lines = explode("\n", trim($log));
    $lastLines = array_slice($lines, -20);
    echo "<h2>Last 20 log lines:</h2>";
    echo "<pre style='background:#f5f5f5; padding:10px; border-radius:5px; font-size:12px;'>";
    echo htmlspecialchars(implode("\n", $lastLines));
    echo "</pre>";
} else {
    echo "<p><b>laravel.log:</b> NOT FOUND</p>";
}
