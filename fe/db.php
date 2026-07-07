<?php
// Database connection config — reads from env vars (Railway) with local fallbacks
$db_host = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost';
$db_user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root';
$db_pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '';
$db_name = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'ecom_clothes_web';
$db_port = (int)($_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? 3306);
