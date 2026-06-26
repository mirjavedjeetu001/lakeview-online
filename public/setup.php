<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Lake View Setup</h1>";

// Check if .env exists
if (!file_exists(__DIR__ . '/.env')) {
    echo "<p style='color:red; font-size:18px;'><b>.env file not found!</b></p>";
    echo "<p>Create .env file here: <code>/home/lakeviex/public_html/.env</code></p>";
    echo "<p>Paste this content:</p>";
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

echo "<p>.env found. Running setup...</p><hr>";

$commands = [
    'key:generate --force' => 'Generate App Key',
    'migrate --force' => 'Run Migrations',
    'storage:link' => 'Storage Symlink',
    'config:cache' => 'Cache Config',
    'route:cache' => 'Cache Routes',
    'view:cache' => 'Cache Views',
];

$results = [];

foreach ($commands as $cmd => $label) {
    echo "<h3>$label</h3><pre style='background:#f5f5f5; padding:10px; border-radius:5px;'>";
    $output = shell_exec("cd " . __DIR__ . " && php artisan $cmd 2>&1");
    echo htmlspecialchars($output ?: '(no output)');
    echo "</pre>";
    $hasError = $output && (strpos($output, 'ERROR') !== false || strpos($output, 'error') !== false);
    $results[] = "$label: " . ($hasError ? 'FAILED' : 'OK');
}

echo "<h2>Summary</h2><ul>";
foreach ($results as $r) {
    $color = strpos($r, 'FAILED') !== false ? 'red' : 'green';
    echo "<li style='color:$color; font-weight:bold;'>$r</li>";
}
echo "</ul>";
echo "<h2 style='color:red'>Delete setup.php now!</h2>";
echo "<p>Then visit: <a href='https://lakeview-cafe.com'>https://lakeview-cafe.com</a></p>";
