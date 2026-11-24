<?php
/**
 * Country Side Cottages - Hostinger Artisan Migration Runner
 * Visit: https://yourdomain.in/run_migrations.php once after extraction.
 */
require __DIR__ . '/../countrysidecottages/vendor/autoload.php';
$app = require_once __DIR__ . '/../countrysidecottages/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><title>Database Migration Runner</title><meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="font-family:system-ui,-apple-system,sans-serif;padding:40px;background:#F8FBF9;color:#1B3B36;">';
echo '<div style="max-width:650px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,0.06);border:1px solid #E2E8F0;">';
echo '<h2 style="margin-top:0;color:#0B5D4B;">Database Migration Helper</h2>';

try {
    $status = $kernel->call('migrate', ['--force' => true]);
    $output = $kernel->output();
    echo '<p style="color:#16A34A;font-weight:600;">&#10004; Migration executed successfully!</p>';
    echo '<pre style="background:#0F172A;color:#38BDF8;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;white-space:pre-wrap;">' . htmlspecialchars($output ?: "Nothing to migrate or already up to date.\nExit code: $status") . '</pre>';
    echo '<p style="color:#DC2626;font-size:13px;font-weight:600;">&#9888; For security, please delete <code>run_migrations.php</code> from public_html now.</p>';
} catch (\Throwable $e) {
    echo '<p style="color:#DC2626;font-weight:600;">&#10008; Migration failed:</p>';
    echo '<pre style="background:#FEF2F2;color:#991B1B;padding:16px;border-radius:8px;font-size:12px;overflow-x:auto;">' . htmlspecialchars($e->getMessage()) . '</pre>';
}

echo '<hr style="border:none;border-top:1px solid #E2E8F0;margin:24px 0;">';
echo '<a href="/" style="display:inline-block;padding:10px 20px;background:#0B5D4B;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">Go to Website Home &rarr;</a>';
echo '</div></body></html>';
