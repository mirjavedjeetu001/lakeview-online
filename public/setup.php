<?php
/**
 * Web-based setup script - Run this ONCE in browser after deploying
 * URL: https://lakeview-cafe.com/setup.php
 * DELETE this file after setup is complete!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Laravel app path (outside public_html)
$LARAVEL_PATH = '/home/lakeviex/lakeview';

echo "<h1>Lake View Setup</h1>";

// Check if Laravel app exists
if (!file_exists($LARAVEL_PATH . '/artisan')) {
    die("ERROR: Laravel app not found at $LARAVEL_PATH");
}

// Check if .env exists
if (!file_exists($LARAVEL_PATH . '/.env')) {
    echo "<p style='color:red; font-size:18px;'><b>ERROR: .env file not found!</b></p>";
    echo "<p>Please create .env file in cPanel File Manager at: <code>$LARAVEL_PATH/.env</code></p>";
    echo "<p>Content to paste:</p>";
    echo "<pre style='background:#f0f0f0; padding:15px; border-radius:8px; font-size:13px;'>";
    $envContent = 'APP_NAME="Lake View Sweets & Bakery"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://lakeview-cafe.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lakeviex_lakeviewonline
DB_USERNAME=lakeviex_lakeviewonline
DB_PASSWORD=JavedMir41@

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file

MAIL_MAILER=log';
    echo htmlspecialchars($envContent);
    echo "</pre>";
    echo "<p>After creating .env, refresh this page.</p>";
    exit;
}

echo "<p>.env file found. Running setup commands...</p><hr>";

$commands = [
    'key:generate --force' => 'Generate App Key',
    'migrate --force' => 'Run Database Migrations',
    'storage:link' => 'Create Storage Symlink',
    'config:cache' => 'Cache Config',
    'route:cache' => 'Cache Routes',
    'view:cache' => 'Cache Views',
];

$results = [];

foreach ($commands as $cmd => $label) {
    echo "<h3>$label</h3>";
    echo "<pre style='background:#f5f5f5; padding:10px; border-radius:5px;'>";
    $fullCmd = "cd $LARAVEL_PATH && php artisan $cmd 2>&1";
    $output = shell_exec($fullCmd);
    echo htmlspecialchars($output ?: '(no output)');
    echo "</pre>";
    $hasError = $output && (strpos($output, 'ERROR') !== false || strpos($output, 'error') !== false);
    $results[] = "$label: " . ($hasError ? 'FAILED' : 'OK');
}

echo "<h2>Setup Summary</h2>";
echo "<ul>";
foreach ($results as $r) {
    $color = strpos($r, 'FAILED') !== false ? 'red' : 'green';
    echo "<li style='color:$color; font-weight:bold;'>$r</li>";
}
echo "</ul>";

echo "<hr><h2 style='color:red'>IMPORTANT: Delete setup.php now!</h2>";
echo "<p>This file is a security risk. Delete it from File Manager.</p>";
echo "<p>Then visit: <a href='https://lakeview-cafe.com'>https://lakeview-cafe.com</a></p>";
