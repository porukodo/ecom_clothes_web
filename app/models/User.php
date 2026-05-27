<?php
require_once __DIR__ . '/../Database.php';

class User {
  // Hàm validation chung
  public static function validateUserData(array $data, bool $isUpdate = false): array {
    $errors = [];

    // 1. Kiểm tra Họ tên (chỉ khi có trong $data)
    if (isset($data['ho_ten'])) {
      if (empty($data['ho_ten'])) {
        $errors['ho_ten'] = 'Họ tên không được để trống.';
      } elseif (!preg_match('/^[A-Za-zÀ-ỹà-ỹ\s\-\\.]+$/u', $data['ho_ten'])) {
        $errors['ho_ten'] = 'Họ tên chỉ được chứa chữ cái, dấu cách, dấu chấm và gạch nối.';
      }
    }

    // 2. Kiểm tra Số điện thoại (chỉ khi có trong $data)
    if (isset($data['so_dien_thoai'])) {
      if (empty($data['so_dien_thoai'])) {
        $errors['so_dien_thoai'] = 'Số điện thoại không được để trống.';
      } elseif (!preg_match('/^0\d{9,10}$/', $data['so_dien_thoai'])) {
        $errors['so_dien_thoai'] = 'Số điện thoại phải bắt đầu bằng 0 và có 10-11 chữ số.';
      }
    }

    // 3. Kiểm tra Email (chỉ khi đăng ký mới - không phải update)
    if (!$isUpdate && isset($data['email'])) {
      if (empty($data['email'])) {
        $errors['email'] = 'Email không được để trống.';
      } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Email không đúng định dạng.';
      } elseif (self::timTheoEmail($data['email'])) {
        $errors['email'] = 'Email này đã được sử dụng.';
      }
    }

    // 4. Kiểm tra Ngày sinh (chỉ khi có trong $data và không rỗng)
    if (isset($data['ngay_sinh']) && $data['ngay_sinh'] !== '') {
      $birthDate = new DateTime($data['ngay_sinh']);
      $today = new DateTime();
      $age = $today->diff($birthDate)->y;
      
      if ($birthDate > $today) {
        $errors['ngay_sinh'] = 'Ngày sinh không thể ở tương lai.';
      } elseif ($age < 13) {
        $errors['ngay_sinh'] = 'Bạn phải đủ 13 tuổi để đăng ký.';
      }
    }

    // 5. Kiểm tra Mật khẩu (khi đổi mật khẩu hoặc đăng ký)
    if (isset($data['mat_khau'])) {
      if (strlen($data['mat_khau']) < 8) {
        $errors['mat_khau'] = 'Mật khẩu phải có ít nhất 8 ký tự.';
      }
    }

    return $errors; // Mảng rỗng = hợp lệ
  }

  public static function timTheoEmail(string $email): ?array {
    $pdo = Database::pdo();
    $stm = $pdo->prepare("SELECT * FROM nguoi_dung WHERE email = :email LIMIT 1");
    $stm->execute(['email' => $email]);
    $row = $stm->fetch();
    return $row ?: null;
  }

    public static function taoMoi(array $data): int {
      // Validate trước khi insert
      $errors = self::validateUserData($data, false);
      if (!empty($errors)) {
        throw new Exception(json_encode(['success' => false, 'errors' => $errors]));
      }

      $pdo = Database::pdo();
      $stm = $pdo->prepare("
        INSERT INTO nguoi_dung (email, mat_khau_bam, ho_ten, ngay_sinh, so_dien_thoai, vai_tro, trang_thai)
        VALUES (:email, :mat_khau_bam, :ho_ten, :ngay_sinh, :so_dien_thoai, :vai_tro, :trang_thai)
      ");
      $stm->execute([
        'email' => $data['email'],
        'mat_khau_bam' => $data['mat_khau_bam'],
        'ho_ten' => $data['ho_ten'] ?? null,
        'ngay_sinh' => $data['ngay_sinh'] ?? null,
        'so_dien_thoai' => $data['so_dien_thoai'] ?? null,
        'vai_tro' => $data['vai_tro'] ?? 'NGUOI_DUNG',
        'trang_thai' => $data['trang_thai'] ?? 'HOAT_DONG',
      ]);
      return (int)$pdo->lastInsertId();
    }


  public static function capNhatLanDangNhapGanNhat(int $id): void {
    $pdo = Database::pdo();
    $stm = $pdo->prepare("UPDATE nguoi_dung SET lan_dang_nhap_gan_nhat = NOW() WHERE id = :id");
    $stm->execute(['id' => $id]);
  }

    public static function capNhatThongTin(int $id, array $data): bool {
      // Validate trước khi update
      $errors = self::validateUserData($data, true);
      if (!empty($errors)) {
        throw new Exception(json_encode(['success' => false, 'errors' => $errors]));
      }

      $pdo = Database::pdo();
      $sql = "UPDATE nguoi_dung 
              SET ho_ten = :ho_ten, 
                  ngay_sinh = :ngay_sinh, 
                  so_dien_thoai = :so_dien_thoai 
              WHERE id = :id";
              
      $stmt = $pdo->prepare($sql);
      return $stmt->execute([
        'ho_ten' => $data['ho_ten'],
        'ngay_sinh' => $data['ngay_sinh'] ?: null,
        'so_dien_thoai' => $data['so_dien_thoai'],
        'id' => $id
      ]);
  }

  // Lấy thông tin user (bao gồm mật khẩu) để kiểm tra
    public static function timTheoId(int $id): ?array {
        $pdo = Database::pdo();
        $stm = $pdo->prepare("SELECT * FROM nguoi_dung WHERE id = :id LIMIT 1");
        $stm->execute(['id' => $id]);
        $row = $stm->fetch();
        return $row ?: null;
    }

    // Cập nhật mật khẩu mới
    public static function capNhatMatKhau(int $id, string $matKhauMoiBam): bool {
        $pdo = Database::pdo();
        $stm = $pdo->prepare("UPDATE nguoi_dung SET mat_khau_bam = :mk WHERE id = :id");
        return $stm->execute([
            'mk' => $matKhauMoiBam,
            'id' => $id
        ]);
    }
}