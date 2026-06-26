<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$LARAVEL_PATH = __DIR__;

echo "<h1>Lake View Setup</h1>";

// Check .env
if (!file_exists($LARAVEL_PATH . '/.env')) {
    echo "<p style='color:red;'><b>.env file not found!</b> Create it at: <code>$LARAVEL_PATH/.env</code></p>";
    exit;
}

// Read current .env, check if APP_KEY is set
$envContent = file_get_contents($LARAVEL_PATH . '/.env');
$hasKey = preg_match('/^APP_KEY=base64:/', $envContent);

echo "<p><b>APP_KEY set:</b> " . ($hasKey ? 'YES' : 'NO') . "</p>";

// Clear cached config first
echo "<h3>Clearing caches...</h3>";
echo "<pre>";
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan config:clear 2>&1") ?? '');
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan cache:clear 2>&1") ?? '');
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan route:clear 2>&1") ?? '');
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan view:clear 2>&1") ?? '');
echo "</pre>";

// Generate key if not set
if (!$hasKey) {
    echo "<h3>Generating App Key...</h3>";
    echo "<pre>";
    echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan key:generate --force 2>&1") ?? '');
    echo "</pre>";
}

// Run migrations
echo "<h3>Running Migrations...</h3>";
echo "<pre>";
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan migrate --force 2>&1") ?? '');
echo "</pre>";

// Storage link
echo "<h3>Storage Link...</h3>";
echo "<pre>";
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan storage:link 2>&1") ?? '');
echo "</pre>";

// Re-cache everything
echo "<h3>Caching Config...</h3>";
echo "<pre>";
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan config:cache 2>&1") ?? '');
echo "</pre>";

echo "<h3>Caching Routes...</h3>";
echo "<pre>";
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan route:cache 2>&1") ?? '');
echo "</pre>";

echo "<h3>Caching Views...</h3>";
echo "<pre>";
echo htmlspecialchars(shell_exec("cd $LARAVEL_PATH && php artisan view:cache 2>&1") ?? '');
echo "</pre>";

// Check build manifest
$manifest = $LARAVEL_PATH . '/build/manifest.json';
echo "<h3>Build Manifest Check</h3>";
echo "<p><b>build/manifest.json:</b> " . (file_exists($manifest) ? 'EXISTS' : 'MISSING') . "</p>";
if (!file_exists($manifest)) {
    $manifestPublic = $LARAVEL_PATH . '/public/build/manifest.json';
    echo "<p><b>public/build/manifest.json:</b> " . (file_exists($manifestPublic) ? 'EXISTS' : 'MISSING') . "</p>";
}

echo "<hr><h2 style='color:red'>Delete setup.php now!</h2>";
echo "<p>Then visit: <a href='https://lakeview-cafe.com'>https://lakeview-cafe.com</a></p>";
