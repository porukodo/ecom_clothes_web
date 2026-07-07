<?php
declare(strict_types=1);

final class Database {
  private static ?\PDO $pdo = null;

  public static function pdo(): \PDO {
    if (self::$pdo) return self::$pdo;

    // Đọc từ env (Railway/production), fallback về config XAMPP local
    $host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');
    $db   = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'ecom_clothes_web');
    $user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
    $pass = getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: ($_ENV['DB_PASSWORD'] ?? $_ENV['DB_PASS'] ?? '');
    $port = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306));

    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

    $options = [
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES => false,
      // đảm bảo collation đúng
      \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ];

    self::$pdo = new \PDO($dsn, $user, $pass, $options);
    // Tương thích Railway MySQL — tắt strict GROUP BY như XAMPP local
    self::$pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    return self::$pdo;
  }
}
