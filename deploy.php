<?php
/**
 * GitHub Webhook Deploy Script
 * Place this file at: /home/lakeviex/public_html/deploy.php
 * Set up GitHub webhook: https://github.com/mirjavedjeetu001/lakeview-online/settings/hooks
 * URL: https://lakeview-cafe.com/deploy.php
 * Content type: application/json
 * Events: Push only (prod branch)
 */

$SECRET = 'lakeview_deploy_secret_2024';
$REPO_DIR = '/home/lakeviex/lakeview';
$PROJECT_DIR = '/home/lakeviex/public_html';
$LOG_FILE = '/home/lakeviex/deploy.log';

function log_msg($msg) {
    global $LOG_FILE;
    file_put_contents($LOG_FILE, date('Y-m-d H:i:s') . ' - ' . $msg . "\n", FILE_APPEND);
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

log_msg('Starting deployment from prod...');

$commands = [
    "cd $REPO_DIR && git fetch --prune origin prod 2>&1",
    "cd $REPO_DIR && git checkout prod 2>&1",
    "cd $REPO_DIR && git pull --ff-only origin prod 2>&1",
    "cd $REPO_DIR && /bin/rsync -av --exclude='public_html' --exclude='.git' --exclude='node_modules' --exclude='.env' --exclude='storage' --exclude='bootstrap/cache' --exclude='public/storage' . $PROJECT_DIR/ 2>&1",
    "cd $REPO_DIR && /bin/mkdir -p $PROJECT_DIR/build $PROJECT_DIR/images 2>&1",
    "cd $REPO_DIR && /bin/rsync -av --delete public/build/ $PROJECT_DIR/build/ 2>&1",
    "cd $REPO_DIR && /bin/rsync -av public/images/ $PROJECT_DIR/images/ 2>&1",
    "cd $REPO_DIR && /bin/cp -f public_html/index.php $PROJECT_DIR/index.php 2>&1",
    "cd $REPO_DIR && /bin/cp -f public_html/.htaccess $PROJECT_DIR/.htaccess 2>&1",
    "/bin/rm -f $PROJECT_DIR/setup.php 2>&1",
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
