<?php
// Database connection config — reads from env vars (Railway) with local fallbacks
$db_host = $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? getenv('DB_HOST') ?? getenv('MYSQLHOST') ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? $_ENV['MYSQLUSER'] ?? getenv('DB_USER') ?? getenv('MYSQLUSER') ?? 'root';
$db_pass = $_ENV['DB_PASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? getenv('DB_PASSWORD') ?? getenv('MYSQLPASSWORD') ?? '';
$db_name = $_ENV['DB_NAME'] ?? $_ENV['MYSQLDATABASE'] ?? getenv('DB_NAME') ?? getenv('MYSQLDATABASE') ?? 'railway';
$db_port = (int)($_ENV['DB_PORT'] ?? $_ENV['MYSQLPORT'] ?? getenv('DB_PORT') ?? getenv('MYSQLPORT') ?? 3306);

function db_connect(string $host, string $user, string $pass, string $name, int $port): mysqli {
    $c = new mysqli($host, $user, $pass, $name, $port);
    if ($c->connect_error) {
        die("DB connection failed: " . $c->connect_error);
    }
    $c->set_charset('utf8mb4');
    // Match local XAMPP behavior — disable strict GROUP BY
    $c->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    return $c;
}
