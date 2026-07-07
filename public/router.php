<?php
// Router for PHP built-in server — replaces .htaccess

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// API routes → backend (public/index.php)
// Frontend calls use the local-dev prefix /public/api/..., accept both
if (str_starts_with($uri, '/api/') || str_starts_with($uri, '/public/api/')) {
    require __DIR__ . '/index.php';
    return;
}

// Static files inside public/ (but never public/index.php — that's the API entry)
if ($uri !== '/' && $uri !== '/index.php' && file_exists(__DIR__ . $uri) && !is_dir(__DIR__ . $uri)) {
    return false;
}

// Static assets from the project root (images/, fe/assets/, ...).
// The server docroot is public/, so these must be streamed manually.
$project_root = realpath(__DIR__ . '/..');
$project_file = realpath($project_root . $uri);
if ($project_file !== false
    && str_starts_with($project_file, $project_root . DIRECTORY_SEPARATOR)
    && is_file($project_file)
    && !str_ends_with(strtolower($project_file), '.php')) {
    $mime_types = [
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon', 'css' => 'text/css', 'js' => 'application/javascript',
        'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf',
        'json' => 'application/json', 'txt' => 'text/plain', 'html' => 'text/html',
    ];
    $ext = strtolower(pathinfo($project_file, PATHINFO_EXTENSION));
    header('Content-Type: ' . ($mime_types[$ext] ?? 'application/octet-stream'));
    header('Content-Length: ' . filesize($project_file));
    readfile($project_file);
    return;
}

// Admin pages (admin-main/)
if (str_starts_with($uri, '/admin-main')) {
    $admin_root = realpath(__DIR__ . '/../admin-main');
    $admin_path = ($uri === '/admin-main' || $uri === '/admin-main/') ? '/admin-main/index.php' : $uri;
    $admin_file = realpath(__DIR__ . '/..' . $admin_path);
    if ($admin_file !== false
        && str_starts_with($admin_file, $admin_root . DIRECTORY_SEPARATOR)
        && is_file($admin_file)) {
        chdir(dirname($admin_file));
        require $admin_file;
        return;
    }
}

// Everything else → frontend (fe/ folder)
$fe_root = __DIR__ . '/../fe';
$fe_file = $fe_root . ($uri === '/' ? '/index.php' : $uri);

// Add .php extension if file not found directly
if (!file_exists($fe_file) && file_exists($fe_file . '.php')) {
    $fe_file = $fe_file . '.php';
}

if (file_exists($fe_file) && !is_dir($fe_file)) {
    chdir(dirname($fe_file));
    require $fe_file;
} else {
    // Fallback to API router (will return 404)
    require __DIR__ . '/index.php';
}
