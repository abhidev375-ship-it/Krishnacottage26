<?php
/**
 * Direct Hostinger Cache & OPcache Reset Tool
 */
header('Content-Type: application/json');

$results = [];

// 1. Reset OPcache
if (function_exists('opcache_reset')) {
    $results['opcache_reset'] = @opcache_reset() ? 'success' : 'failed';
}

// 2. Locate storage folder
$possiblePaths = [
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../countrysidecottages/storage/framework/views',
    dirname(__DIR__) . '/storage/framework/views',
];

$deleted = 0;
foreach ($possiblePaths as $path) {
    if (is_dir($path)) {
        foreach (glob($path . '/*.php') as $file) {
            @unlink($file);
            $deleted++;
        }
    }
}
$results['compiled_views_deleted'] = $deleted;

echo json_encode([
    'status' => 'success',
    'message' => 'Cache and OPcache flushed successfully. Reload the admin panel now!',
    'details' => $results,
    'time' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);
