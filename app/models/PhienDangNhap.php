<?php
declare(strict_types=1);

require_once __DIR__ . '/../Database.php';

final class PhienDangNhap
{
  /** Tạo phiên đăng nhập mới, trả về token */
  public static function tao(int $nguoi_dung_id, int $ttlDays = 7): string
  {
    $pdo = Database::pdo();

    // token unique (cột token đang UNIQUE)
    for ($i = 0; $i < 5; $i++) {
      $token = bin2hex(random_bytes(32)); // 64 chars

      try {
        $stm = $pdo->prepare("
          INSERT INTO phien_dang_nhap (nguoi_dung_id, token, het_han_luc)
          VALUES (:uid, :token, DATE_ADD(NOW(), INTERVAL :days DAY))
        ");
        $stm->bindValue(':uid', $nguoi_dung_id, PDO::PARAM_INT);
        $stm->bindValue(':token', $token, PDO::PARAM_STR);
        $stm->bindValue(':days', $ttlDays, PDO::PARAM_INT);
        $stm->execute();

        return $token;
      } catch (\PDOException $e) {
        // 1062 = duplicate key (token trùng hiếm), thử token khác
        if ((int)($e->errorInfo[1] ?? 0) === 1062) continue;
        throw $e;
      }
    }

    throw new \RuntimeException('Không tạo được token phiên đăng nhập');
  }

  /** Thu hồi (logout) theo token */
  public static function thuHoi(string $token): void
  {
    $pdo = Database::pdo();
    $stm = $pdo->prepare("
      UPDATE phien_dang_nhap
      SET thu_hoi_luc = NOW()
      WHERE token = :token
        AND thu_hoi_luc IS NULL
      LIMIT 1
    ");
    $stm->execute(['token' => $token]);
  }
  public static function dong(int $id): void
    {
        $pdo = Database::pdo();
        $stm = $pdo->prepare("
            UPDATE phien_dang_nhap
            SET thu_hoi_luc = NOW()
            WHERE id = :id
              AND thu_hoi_luc IS NULL
            LIMIT 1
        ");
        $stm->execute(['id' => $id]);
    }
}