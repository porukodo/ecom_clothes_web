<?php
/**
 * Seed script — run once via Railway URL, then delete.
 * 1. Removes all non-admin users
 * 2. Inserts 15 realistic Vietnamese customer accounts
 * 3. Inserts ~55 orders spread through June–July 2026
 *
 * Usage: GET /seed_data.php?token=SEED_ECOM_2026
 */

if (($_GET['token'] ?? '') !== 'SEED_ECOM_2026') {
    http_response_code(403);
    exit('Forbidden');
}

// ── DB connection (Railway env vars) ────────────────────────────────────────
$host    = getenv('DB_HOST')     ?: '127.0.0.1';
$dbname  = getenv('DB_NAME')     ?: 'ecom_clothes_web';
$user    = getenv('DB_USER')     ?: 'root';
$pass    = getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: '';
$port    = (int)(getenv('DB_PORT') ?: 3306);

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
} catch (PDOException $e) {
    die("DB error: " . $e->getMessage());
}

$log = [];

// ── 1. Delete non-admin users (cascades to carts, sessions, etc.) ───────────
$pdo->exec("SET FOREIGN_KEY_CHECKS=0");
$deleted = $pdo->exec("DELETE FROM nguoi_dung WHERE vai_tro = 'NGUOI_DUNG'");
$pdo->exec("SET FOREIGN_KEY_CHECKS=1");
$log[] = "✓ Deleted $deleted non-admin user accounts";

// ── 2. Insert 15 realistic customer accounts ─────────────────────────────────
$hash = password_hash('123456', PASSWORD_DEFAULT);

$customers = [
    // [id, email, ho_ten, so_dien_thoai, tao_luc]
    [10001, 'nguyenvana@gmail.com',    'Nguyễn Văn An',      '0901234567', '2026-05-10 09:00:00'],
    [10002, 'tranthib@gmail.com',      'Trần Thị Bích',      '0912345678', '2026-05-12 14:00:00'],
    [10003, 'lehongc@gmail.com',       'Lê Hồng Chiến',      '0923456789', '2026-05-15 10:30:00'],
    [10004, 'phamthid@gmail.com',      'Phạm Thị Dung',      '0934567890', '2026-05-18 08:45:00'],
    [10005, 'vuminhe@gmail.com',       'Vũ Minh Đức',        '0945678901', '2026-05-20 16:00:00'],
    [10006, 'hoangthif@gmail.com',     'Hoàng Thị Giang',    '0956789012', '2026-05-22 11:20:00'],
    [10007, 'dangvang@gmail.com',      'Đặng Văn Hùng',      '0967890123', '2026-05-25 13:00:00'],
    [10008, 'buithih@gmail.com',       'Bùi Thị Hương',      '0978901234', '2026-05-28 09:30:00'],
    [10009, 'ngothii@gmail.com',       'Ngô Thị Kim',        '0989012345', '2026-06-01 10:00:00'],
    [10010, 'dinhvank@gmail.com',      'Đinh Văn Long',      '0990123456', '2026-06-03 15:00:00'],
    [10011, 'nguyenthil@gmail.com',    'Nguyễn Thị Mai',     '0901112233', '2026-06-05 08:00:00'],
    [10012, 'tranvanm@gmail.com',      'Trần Văn Nam',       '0912223344', '2026-06-08 17:30:00'],
    [10013, 'lehoangn@gmail.com',      'Lê Hoàng Phúc',      '0923334455', '2026-06-10 12:00:00'],
    [10014, 'phamvano@gmail.com',      'Phạm Văn Quân',      '0934445566', '2026-06-12 09:00:00'],
    [10015, 'votheip@gmail.com',       'Võ Thị Phương',      '0945556677', '2026-06-15 14:00:00'],
];

$insertUser = $pdo->prepare(
    "INSERT IGNORE INTO nguoi_dung (id, email, mat_khau_bam, ho_ten, so_dien_thoai, vai_tro, trang_thai, tao_luc)
     VALUES (?, ?, ?, ?, ?, 'NGUOI_DUNG', 'HOAT_DONG', ?)"
);
foreach ($customers as $c) {
    $insertUser->execute([$c[0], $c[1], $hash, $c[2], $c[3], $c[4]]);
}
$log[] = "✓ Inserted " . count($customers) . " customer accounts (password: 123456)";

// ── 3. Seed orders ────────────────────────────────────────────────────────────
// product_id => [ [sku_id, don_gia, ten_san_pham], ... ]
$products = [
    19 => [[2019, 199000, 'Áo thun Basic Cotton'], [2020, 199000, 'Áo thun Basic Cotton'], [2021, 199000, 'Áo thun Basic Cotton']],
    20 => [[2054, 259000, 'Áo thun Oversize Street'], [2058, 259000, 'Áo thun Oversize Street'], [2062, 259000, 'Áo thun Oversize Street']],
    21 => [[2070, 299000, 'Áo thun Polo Minimal'], [2073, 299000, 'Áo thun Polo Minimal'], [2076, 299000, 'Áo thun Polo Minimal']],
    24 => [[2315, 499000, 'Quần Jeans Slim Fit'], [2311, 499000, 'Quần Jeans Slim Fit'], [2307, 499000, 'Quần Jeans Slim Fit']],
    25 => [[2316, 429000, 'Quần Kaki Regular'], [2312, 429000, 'Quần Kaki Regular'], [2308, 429000, 'Quần Kaki Regular']],
    26 => [[2093, 249000, 'Quần Short Linen'], [2095, 249000, 'Quần Short Linen'], [2097, 249000, 'Quần Short Linen']],
    27 => [[2030, 699000, 'Áo khoác Bomber'], [2031, 699000, 'Áo khoác Bomber'], [2034, 699000, 'Áo khoác Bomber']],
    28 => [[2309, 749000, 'Áo khoác Denim Jacket'], [2313, 749000, 'Áo khoác Denim Jacket']],
    31 => [[2023, 399000, 'Hoodie Oversize Basic'], [2025, 399000, 'Hoodie Oversize Basic'], [2027, 419000, 'Hoodie Oversize Basic']],
    32 => [[2145, 459000, 'Hoodie Zip Street'], [2147, 459000, 'Hoodie Zip Street']],
];
$productIds = array_keys($products);

$addresses = [
    ['Nguyễn Văn An',    '0901234567', '15 Lê Lợi, Quận 1, TP.HCM'],
    ['Trần Thị Bích',    '0912345678', '22 Nguyễn Huệ, Hoàn Kiếm, Hà Nội'],
    ['Lê Hồng Chiến',    '0923456789', '8 Trần Phú, Đà Nẵng'],
    ['Phạm Thị Dung',    '0934567890', '56 Lý Thường Kiệt, Quận 10, TP.HCM'],
    ['Vũ Minh Đức',      '0945678901', '3 Đinh Tiên Hoàng, Bình Thạnh, TP.HCM'],
    ['Hoàng Thị Giang',  '0956789012', '99 Cách Mạng Tháng Tám, Quận 3, TP.HCM'],
    ['Đặng Văn Hùng',    '0967890123', '17 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội'],
    ['Bùi Thị Hương',    '0978901234', '45 Ngô Quyền, Hải Châu, Đà Nẵng'],
    ['Ngô Thị Kim',      '0989012345', '30 Phan Đình Phùng, Phú Nhuận, TP.HCM'],
    ['Đinh Văn Long',    '0990123456', '12 Bùi Thị Xuân, Hai Bà Trưng, Hà Nội'],
    ['Nguyễn Thị Mai',   '0901112233', '88 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM'],
    ['Trần Văn Nam',     '0912223344', '5 Ngô Gia Tự, Long Biên, Hà Nội'],
    ['Lê Hoàng Phúc',    '0923334455', '20 Lê Duẩn, Quận 1, TP.HCM'],
    ['Phạm Văn Quân',    '0934445566', '33 Trường Chinh, Thanh Xuân, Hà Nội'],
    ['Võ Thị Phương',    '0945556677', '7 Trần Bạch Đằng, Hải Châu, Đà Nẵng'],
];

// 55 orders: dates spread across June 1 – July 8, 2026
$orderDates = [
    '2026-06-01 09:15:00', '2026-06-01 14:30:00',
    '2026-06-02 10:00:00',
    '2026-06-03 16:45:00',
    '2026-06-04 11:20:00', '2026-06-04 18:00:00',
    '2026-06-05 08:30:00',
    '2026-06-06 13:10:00', '2026-06-06 15:55:00',
    '2026-06-07 09:40:00',
    '2026-06-08 12:00:00', '2026-06-08 17:20:00',
    '2026-06-09 10:30:00',
    '2026-06-10 14:00:00',
    '2026-06-11 09:00:00', '2026-06-11 16:30:00',
    '2026-06-12 11:45:00',
    '2026-06-13 08:20:00', '2026-06-13 14:50:00',
    '2026-06-14 10:00:00',
    '2026-06-15 13:30:00', '2026-06-15 18:10:00',
    '2026-06-16 09:20:00',
    '2026-06-17 12:45:00',
    '2026-06-18 15:00:00', '2026-06-18 10:30:00',
    '2026-06-19 11:00:00',
    '2026-06-20 09:30:00', '2026-06-20 16:00:00',
    '2026-06-21 14:20:00',
    '2026-06-22 10:10:00', '2026-06-22 17:40:00',
    '2026-06-23 08:50:00',
    '2026-06-24 12:00:00',
    '2026-06-25 15:30:00', '2026-06-25 10:00:00',
    '2026-06-26 09:15:00',
    '2026-06-27 13:00:00', '2026-06-27 17:00:00',
    '2026-06-28 11:20:00',
    '2026-06-29 09:00:00', '2026-06-29 14:30:00',
    '2026-06-30 10:45:00', '2026-06-30 16:20:00',
    '2026-07-01 09:30:00', '2026-07-01 15:00:00',
    '2026-07-02 11:00:00',
    '2026-07-03 13:45:00', '2026-07-03 17:30:00',
    '2026-07-04 09:00:00',
    '2026-07-05 10:30:00', '2026-07-05 14:00:00',
    '2026-07-06 11:15:00', '2026-07-06 16:45:00',
    '2026-07-07 09:30:00',
    '2026-07-08 10:00:00',
];

// Status distribution by order date (older = more likely completed)
function pickStatus(string $date): string {
    $day = (int)date('d', strtotime(explode(' ', $date)[0]));
    $month = (int)date('m', strtotime(explode(' ', $date)[0]));
    // July orders: mix of pending/processing
    if ($month === 7) {
        $pool = ['CHO_XU_LY','CHO_XU_LY','DANG_XU_LY','DANG_XU_LY','HOAN_TAT'];
    } elseif ($day >= 20) {
        // Late June: mostly processing/completed
        $pool = ['CHO_XU_LY','DANG_XU_LY','DANG_XU_LY','HOAN_TAT','HOAN_TAT','HOAN_TAT'];
    } else {
        // Early-mid June: mostly completed, some cancelled
        $pool = ['HOAN_TAT','HOAN_TAT','HOAN_TAT','HOAN_TAT','HUY','DANG_XU_LY'];
    }
    return $pool[array_rand($pool)];
}

$insertOrder = $pdo->prepare(
    "INSERT INTO don_hang (ma_don_hang, nguoi_dung_id, trang_thai, phuong_thuc_thanh_toan,
     trang_thai_thanh_toan, tam_tinh, phi_van_chuyen, giam_gia, tong_tien,
     nguoi_nhan, sdt_nguoi_nhan, dia_chi_giao_hang, tao_luc, cap_nhat_luc)
     VALUES (?, ?, ?, 'COD', ?, ?, 30000, 0, ?, ?, ?, ?, ?, ?)"
);
$insertItem = $pdo->prepare(
    "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, sku_id, ten_san_pham, don_gia, so_luong, thanh_tien, tao_luc)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

$customerIds = array_column($customers, 0);
$orderCount  = 0;
$orderNum    = 5001; // start MA_DON_HANG counter

foreach ($orderDates as $date) {
    $custIdx  = array_rand($customerIds);
    $custId   = $customerIds[$custIdx];
    $addr     = $addresses[$custIdx];
    $status   = pickStatus($date);
    $payStatus = ($status === 'HOAN_TAT') ? 'DA_THANH_TOAN' : 'CHUA_THANH_TOAN';

    // 1–3 product lines per order
    $lineCount = rand(1, 3);
    shuffle($productIds);
    $chosenPids = array_slice($productIds, 0, $lineCount);

    $tamTinh = 0;
    $lines = [];
    foreach ($chosenPids as $pid) {
        $skuList = $products[$pid];
        $sku     = $skuList[array_rand($skuList)];
        $qty     = rand(1, 2);
        $price   = $sku[1];
        $sub     = $price * $qty;
        $tamTinh += $sub;
        $lines[] = [$pid, $sku[0], $sku[2], $price, $qty, $sub];
    }
    $tongTien = $tamTinh + 30000; // + shipping

    $maOrder = 'DH' . $orderNum++;
    $insertOrder->execute([
        $maOrder, $custId, $status, $payStatus,
        $tamTinh, $tongTien,
        $addr[0], $addr[1], $addr[2],
        $date, $date,
    ]);
    $orderId = $pdo->lastInsertId();

    foreach ($lines as $ln) {
        $insertItem->execute([$orderId, $ln[0], $ln[1], $ln[2], $ln[3], $ln[4], $ln[5], $date]);
    }
    $orderCount++;
}

$log[] = "✓ Inserted $orderCount orders across June–July 2026";

// ── Summary ──────────────────────────────────────────────────────────────────
header('Content-Type: text/plain; charset=utf-8');
echo "=== Seed complete ===\n\n";
foreach ($log as $line) {
    echo $line . "\n";
}
echo "\n⚠️  Delete this file: public/seed_data.php\n";
