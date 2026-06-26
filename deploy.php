<?php
/**
 * GitHub Webhook Deploy Script
 * Place this file at: /home/lakeviex/deploy.php
 * Set up GitHub webhook: https://github.com/mirjavedjeetu001/lakeview-online/settings/hooks
 * URL: https://lakeview-cafe.com/deploy.php
 * Content type: application/json
 * Events: Push only (prod branch)
 */

$SECRET = 'lakeview_deploy_secret_2024';
$PROJECT_DIR = '/home/lakeviex/lakeview';
$LOG_FILE = '/home/lakeviex/deploy.log';

function log_msg($msg) {
    file_put_contents(__DIR__ . '/deploy.log', date('Y-m-d H:i:s') . ' - ' . $msg . "\n", FILE_APPEND);
}

// Get the payload
$payload = file_get_contents('php://input');
$signature = 'sha256=' . hash_hmac('sha256', $payload, $SECRET);

// Verify signature
if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE_256']) || !hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
    http_response_code(403);
    log_msg('ERROR: Invalid signature');
    exit('Forbidden');
}

$data = json_decode($payload, true);

// Only deploy on prod branch
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/prod') {
    http_response_code(200);
    log_msg('Skipped: not prod branch');
    exit('Skipped');
}

log_msg('Starting deployment...');

$commands = [
    "cd $PROJECT_DIR && git fetch origin",
    "cd $PROJECT_DIR && git checkout prod",
    "cd $PROJECT_DIR && git pull origin prod",
    "cd $PROJECT_DIR && composer install --no-dev --optimize-autoloader 2>&1",
    "cd $PROJECT_DIR && npm install 2>&1",
    "cd $PROJECT_DIR && npm run build 2>&1",
    "cd $PROJECT_DIR && php artisan migrate --force 2>&1",
    "cd $PROJECT_DIR && php artisan config:cache 2>&1",
    "cd $PROJECT_DIR && php artisan route:cache 2>&1",
    "cd $PROJECT_DIR && php artisan view:cache 2>&1",
    "cd $PROJECT_DIR && chmod -R 775 storage bootstrap/cache 2>&1",
];

foreach ($commands as $cmd) {
    $output = shell_exec($cmd . ' 2>&1');
    log_msg("CMD: $cmd");
    log_msg("OUT: $output");
}

log_msg('Deployment complete!');
http_response_code(200);
echo 'Deployed successfully';
