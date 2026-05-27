<?php
declare(strict_types=1);

final class Database {
  private static ?\PDO $pdo = null;

  public static function pdo(): \PDO {
    if (self::$pdo) return self::$pdo;

    // Config cứng cho XAMPP local (MVP)
    $host = '127.0.0.1';
    $db   = 'PTUD_Final';
    $user = 'root';
    $pass = '';        
    $port = 3306;

    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

    $options = [
      \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES => false,
      // đảm bảo collation đúng
      \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
    ];

    self::$pdo = new \PDO($dsn, $user, $pass, $options);
    return self::$pdo;
  }
}
