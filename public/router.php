<?php
// Router for PHP built-in server — replaces .htaccess

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve existing static files from public/ directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Serve images/ and other assets from project root
$project_file = __DIR__ . '/..' . $uri;
if ($uri !== '/' && file_exists($project_file) && !is_dir($project_file)) {
    return false;
}

// API routes → index.php (backend)
if (str_starts_with($uri, '/api/')) {
    require __DIR__ . '/index.php';
    return;
}

// Everything else → frontend (fe/ folder)
$fe_root = __DIR__ . '/../fe';

// Map URI to a frontend file
$fe_file = $fe_root . ($uri === '/' ? '/index.php' : $uri);

// Add .php extension if file not found directly
if (!file_exists($fe_file) && file_exists($fe_file . '.php')) {
    $fe_file = $fe_file . '.php';
}

if (file_exists($fe_file) && !is_dir($fe_file)) {
    // Set the working directory context for relative includes inside fe/
    chdir($fe_root);
    require $fe_file;
} else {
    // Fallback to API router (will return 404)
    require __DIR__ . '/index.php';
}
