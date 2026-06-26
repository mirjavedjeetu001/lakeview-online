<?php
/**
 * Web-based setup script - Run this ONCE in browser after deploying
 * URL: https://lakeview-cafe.com/setup.php
 * DELETE this file after setup is complete!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$commands = [
    'key:generate --force' => 'Generate App Key',
    'migrate --force' => 'Run Database Migrations',
    'storage:link' => 'Create Storage Symlink',
    'config:cache' => 'Cache Config',
    'route:cache' => 'Cache Routes',
    'view:cache' => 'Cache Views',
];

$results = [];

// Check if .env exists
if (!file_exists(__DIR__ . '/.env')) {
    die('ERROR: .env file not found. Please create it first in cPanel File Manager.');
}

foreach ($commands as $cmd => $label) {
    echo "<h3>$label</h3>";
    echo "<pre>";
    $fullCmd = "php " . __DIR__ . "/artisan $cmd 2>&1";
    $output = shell_exec($fullCmd);
    echo htmlspecialchars($output ?: '(no output)');
    echo "</pre>";
    $results[] = "$label: " . (str_contains($output ?? '', 'ERROR') ? 'FAILED' : 'OK');
}

echo "<h2>Setup Summary</h2>";
echo "<ul>";
foreach ($results as $r) echo "<li>$r</li>";
echo "</ul>";

echo "<h2 style='color:red'>IMPORTANT: Delete setup.php now!</h2>";
echo "<p>This file is a security risk. Delete it from File Manager.</p>";
