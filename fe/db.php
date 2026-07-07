<?php
// Database connection config — reads from env vars (Railway) with local fallbacks
// Try Railway's DB_* naming first, then MYSQL* naming
$db_host = $_ENV['DB_HOST'] ?? $_ENV['MYSQLHOST'] ?? getenv('DB_HOST') ?? getenv('MYSQLHOST') ?? 'localhost';
$db_user = $_ENV['DB_USER'] ?? $_ENV['MYSQLUSER'] ?? getenv('DB_USER') ?? getenv('MYSQLUSER') ?? 'root';
$db_pass = $_ENV['DB_PASSWORD'] ?? $_ENV['MYSQLPASSWORD'] ?? getenv('DB_PASSWORD') ?? getenv('MYSQLPASSWORD') ?? '';
$db_name = $_ENV['DB_NAME'] ?? $_ENV['MYSQLDATABASE'] ?? getenv('DB_NAME') ?? getenv('MYSQLDATABASE') ?? 'railway';
$db_port = (int)($_ENV['DB_PORT'] ?? $_ENV['MYSQLPORT'] ?? getenv('DB_PORT') ?? getenv('MYSQLPORT') ?? 3306);
