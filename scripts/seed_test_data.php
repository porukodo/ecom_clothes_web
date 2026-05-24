<?php
/**
 * Seed ~4 000 benchmark users, addresses, and orders with encrypted PII.
 *
 * Usage:
 *   php scripts/seed_test_data.php           # skip if already seeded
 *   php scripts/seed_test_data.php --force   # wipe and re-seed
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

$root = dirname(__DIR__);
require_once $root . '/app/Database.php';
require_once $root . '/app/bootstrap.php';
require_once $root . '/app/security/KeyManager.php';
require_once $root . '/app/security/EncryptionService.php';

$force  = in_array('--force', $argv ?? [], true);
$pdo    = Database::pdo();
$useEnc = KeyManager::isEnabled();

// ── Check existing ───────────────────────────────────────────────────────────
$existing = (int) $pdo
    ->query("SELECT COUNT(*) FROM nguoi_dung WHERE email LIKE 'bm%@bench.local'")
    ->fetchColumn();

if ($existing >= 3_000 && !$force) {
    echo "✓ Already seeded {$existing} benchmark users. Use --force to re-seed.\n";
    exit(0);
}

if ($force && $existing > 0) {
    echo "Removing {$existing} existing benchmark records... ";
    $ids = $pdo
        ->query("SELECT id FROM nguoi_dung WHERE email LIKE 'bm%@bench.local'")
        ->fetchAll(PDO::FETCH_COLUMN);

    if ($ids) {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $pdo->prepare("DELETE FROM dia_chi  WHERE nguoi_dung_id IN ({$ph})")->execute($ids);
        $pdo->prepare("DELETE FROM don_hang WHERE nguoi_dung_id IN ({$ph})")->execute($ids);
        $pdo->prepare("DELETE FROM nguoi_dung WHERE id          IN ({$ph})")->execute($ids);
    }
    echo "done.\n";
}

// ── Vietnamese data pools ────────────────────────────────────────────────────
$ho    = ['Nguyễn','Trần','Lê','Phạm','Hoàng','Huỳnh','Phan','Vũ','Võ','Đặng',
          'Bùi','Đỗ','Hồ','Ngô','Dương','Lý','Đinh','Đoàn','Mai','Lưu'];
$dem   = ['Văn','Thị','Minh','Quốc','Anh','Bảo','Hữu','Đức','Thành','Kim',
          'Thanh','Trung','Bích','Thu','Ngọc','Hồng','Phương','Xuân','Diệu','Tấn'];
$ten   = ['An','Bình','Chi','Dung','Giang','Hà','Hải','Hoa','Hùng','Lan',
          'Linh','Long','Mai','Nam','Nga','Phát','Phương','Quân','Sơn','Tâm',
          'Thảo','Tuấn','Uyên','Vân','Xuân','Yến','Khoa','Toàn','Hiếu','Đạt'];
$tinh  = ['Hà Nội','TP. Hồ Chí Minh','Đà Nẵng','Cần Thơ','Hải Phòng',
          'Bình Dương','Đồng Nai','Khánh Hòa','Nghệ An','Thanh Hóa',
          'Bình Định','Thừa Thiên Huế','Lâm Đồng','An Giang','Kiên Giang'];
$duong = ['Trần Hưng Đạo','Lê Lợi','Nguyễn Huệ','Điện Biên Phủ','Hai Bà Trưng',
          'Lý Thường Kiệt','Nguyễn Trãi','Cách Mạng Tháng 8','Phan Đình Phùng',
          'Hoàng Văn Thụ','Lê Duẩn','Phạm Ngọc Thạch','Nguyễn Đình Chiểu'];
$pref  = ['032','033','034','035','036','037','038','039',
          '070','076','077','078','079','086','096','097','098'];
$stats = ['CHO_XU_LY','DANG_XU_LY','HOAN_TAT','HOAN_TAT','HOAN_TAT'];

$hashPass = password_hash('Benchmark@2025', PASSWORD_BCRYPT);
$total    = 4_000;
$t0       = microtime(true);

function enc(string $v): string
{
    global $useEnc;
    return $useEnc ? (EncryptionService::encrypt($v) ?? $v) : $v;
}

echo "━━━ Seeding {$total} benchmark users ";
echo "(mã hóa: " . ($useEnc ? 'BẬT — AES-256-GCM' : 'TẮT') . ") ━━━\n";

// ── Users + Addresses ────────────────────────────────────────────────────────
$userIds = [];
$pdo->beginTransaction();

for ($i = 1; $i <= $total; $i++) {
    $hoTen = $ho[array_rand($ho)] . ' ' . $dem[array_rand($dem)] . ' ' . $ten[array_rand($ten)];
    $phone = $pref[array_rand($pref)]
           . str_pad((string) random_int(1_000_000, 9_999_999), 7, '0', STR_PAD_LEFT);
    $dob   = sprintf('%04d-%02d-%02d',
                 random_int(1970, 2004), random_int(1, 12), random_int(1, 28));

    $pdo->prepare("
        INSERT INTO nguoi_dung
            (email, mat_khau_bam, ho_ten, so_dien_thoai, ngay_sinh, vai_tro, trang_thai)
        VALUES (?, ?, ?, ?, ?, 'NGUOI_DUNG', 'HOAT_DONG')
    ")->execute(["bm{$i}@bench.local", $hashPass, enc($hoTen), enc($phone), enc($dob)]);

    $uid       = (int) $pdo->lastInsertId();
    $userIds[] = $uid;

    $t = $tinh[array_rand($tinh)];
    $q = 'Quận ' . random_int(1, 12);
    $p = 'Phường ' . random_int(1, 20);
    $a = random_int(1, 500) . ' ' . $duong[array_rand($duong)];

    $pdo->prepare("
        INSERT INTO dia_chi
            (nguoi_dung_id, ten_nguoi_nhan, so_dien_thoai,
             tinh_thanh, quan_huyen, phuong_xa, dia_chi_cu_the, mac_dinh)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ")->execute([$uid, enc($hoTen), enc($phone), enc($t), enc($q), enc($p), enc($a)]);

    if ($i % 500 === 0) {
        $pdo->commit();
        printf("  [%4d / %4d] %.1fs\n", $i, $total, microtime(true) - $t0);
        $pdo->beginTransaction();
    }
}
$pdo->commit();

// ── Orders ───────────────────────────────────────────────────────────────────
$orders = 2_000;
echo "Seeding {$orders} benchmark orders...\n";
$pdo->beginTransaction();

for ($i = 1; $i <= $orders; $i++) {
    $uid     = $userIds[array_rand($userIds)];
    $ma      = 'BENCH-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT);
    $stat    = $stats[array_rand($stats)];
    $subtotal = random_int(100, 5_000) * 1_000;
    $ship    = 30_000;
    $total_p = $subtotal + $ship;

    // Tier C PII fields on don_hang are also encrypted (PiiFields::ORDER)
    $tenNhan = enc($ho[array_rand($ho)] . ' ' . $dem[array_rand($dem)] . ' ' . $ten[array_rand($ten)]);
    $sdtNhan = enc($pref[array_rand($pref)]
             . str_pad((string) random_int(1_000_000, 9_999_999), 7, '0', STR_PAD_LEFT));
    $dcgh    = enc(random_int(1, 500) . ' ' . $duong[array_rand($duong)]
             . ', ' . $tinh[array_rand($tinh)]);

    $pdo->prepare("
        INSERT INTO don_hang
            (ma_don_hang, nguoi_dung_id, trang_thai, phuong_thuc_thanh_toan,
             trang_thai_thanh_toan, tam_tinh, phi_van_chuyen, giam_gia, tong_tien,
             nguoi_nhan, sdt_nguoi_nhan, dia_chi_giao_hang)
        VALUES (?, ?, ?, 'COD', 'CHUA_THANH_TOAN', ?, ?, 0, ?, ?, ?, ?)
    ")->execute([$ma, $uid, $stat, $subtotal, $ship, $total_p,
                 $tenNhan, $sdtNhan, $dcgh]);

    if ($i % 500 === 0) {
        $pdo->commit();
        $pdo->beginTransaction();
    }
}
$pdo->commit();

printf("\n✓ Hoàn thành trong %.1fs — %d người dùng · %d địa chỉ · %d đơn hàng.\n",
    microtime(true) - $t0, $total, $total, $orders);
