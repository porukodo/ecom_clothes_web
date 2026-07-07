<?php
// Router script for PHP built-in server
// This handles the rewriting that .htaccess normally does

$requested_file = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the requested file exists (static files), serve it directly
if (file_exists(__DIR__ . $requested_file)) {
    return false; // Let PHP server handle the static file
}

// Otherwise, route to index.php (like .htaccess does)
require __DIR__ . '/index.php';
