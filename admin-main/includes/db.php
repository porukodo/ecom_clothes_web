<?php
// includes/db.php - Kết nối Database
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

// Đọc từ env (Railway/production), fallback về config XAMPP local
define('DB_HOST', getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost'));
define('DB_NAME', getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'ecom_clothes_web'));
define('DB_USER', getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root'));
define('DB_PASS', getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: ($_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? ''));
define('DB_PORT', (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306)));
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    // Tương thích Railway MySQL — tắt strict GROUP BY như XAMPP local
    $pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}
