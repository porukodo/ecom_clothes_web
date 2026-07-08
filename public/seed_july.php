<?php
// One-time script: add more July orders spread across July 1–8.
// Token-protected. DELETE after use.
if (($_GET['token'] ?? '') !== 'SEED_JULY_2026') {
    http_response_code(403); exit('Forbidden');
}

$host   = getenv('DB_HOST')     ?: '127.0.0.1';
$dbname = getenv('DB_NAME')     ?: 'ecom_clothes_web';
$user   = getenv('DB_USER')     ?: 'root';
$pass   = getenv('DB_PASSWORD') ?: getenv('DB_PASS') ?: '';
$port   = (int)(getenv('DB_PORT') ?: 3306);

$pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
$pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

$products = [
    19 => [[2019, 199000, 'Áo thun Basic Cotton'], [2020, 199000, 'Áo thun Basic Cotton'], [2021, 199000, 'Áo thun Basic Cotton']],
    20 => [[2054, 259000, 'Áo thun Oversize Street'], [2058, 259000, 'Áo thun Oversize Street']],
    21 => [[2070, 299000, 'Áo thun Polo Minimal'], [2073, 299000, 'Áo thun Polo Minimal']],
    24 => [[2315, 499000, 'Quần Jeans Slim Fit'], [2311, 499000, 'Quần Jeans Slim Fit']],
    25 => [[2316, 429000, 'Quần Kaki Regular'], [2312, 429000, 'Quần Kaki Regular']],
    26 => [[2093, 249000, 'Quần Short Linen'], [2095, 249000, 'Quần Short Linen']],
    27 => [[2030, 699000, 'Áo khoác Bomber'], [2031, 699000, 'Áo khoác Bomber']],
    31 => [[2023, 399000, 'Hoodie Oversize Basic'], [2025, 399000, 'Hoodie Oversize Basic']],
    32 => [[2145, 459000, 'Hoodie Zip Street']],
];
$productIds = array_keys($products);

// Customer IDs seeded previously
$customerIds = range(10001, 10015);

$addresses = [
    10001 => ['Nguyễn Văn An',   '0901234567', '15 Lê Lợi, Quận 1, TP.HCM'],
    10002 => ['Trần Thị Bích',   '0912345678', '22 Nguyễn Huệ, Hoàn Kiếm, Hà Nội'],
    10003 => ['Lê Hồng Chiến',   '0923456789', '8 Trần Phú, Đà Nẵng'],
    10004 => ['Phạm Thị Dung',   '0934567890', '56 Lý Thường Kiệt, Quận 10, TP.HCM'],
    10005 => ['Vũ Minh Đức',     '0945678901', '3 Đinh Tiên Hoàng, Bình Thạnh, TP.HCM'],
    10006 => ['Hoàng Thị Giang', '0956789012', '99 Cách Mạng Tháng Tám, Quận 3, TP.HCM'],
    10007 => ['Đặng Văn Hùng',   '0967890123', '17 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội'],
    10008 => ['Bùi Thị Hương',   '0978901234', '45 Ngô Quyền, Hải Châu, Đà Nẵng'],
    10009 => ['Ngô Thị Kim',     '0989012345', '30 Phan Đình Phùng, Phú Nhuận, TP.HCM'],
    10010 => ['Đinh Văn Long',   '0990123456', '12 Bùi Thị Xuân, Hai Bà Trưng, Hà Nội'],
    10011 => ['Nguyễn Thị Mai',  '0901112233', '88 Điện Biên Phủ, Quận Bình Thạnh, TP.HCM'],
    10012 => ['Trần Văn Nam',    '0912223344', '5 Ngô Gia Tự, Long Biên, Hà Nội'],
    10013 => ['Lê Hoàng Phúc',   '0923334455', '20 Lê Duẩn, Quận 1, TP.HCM'],
    10014 => ['Phạm Văn Quân',   '0934445566', '33 Trường Chinh, Thanh Xuân, Hà Nội'],
    10015 => ['Võ Thị Phương',   '0945556677', '7 Trần Bạch Đằng, Hải Châu, Đà Nẵng'],
];

// ~3–5 orders per remaining day of July (days 2-8 already have some; pad them all out)
$orderDates = [
    '2026-07-01 08:30:00', '2026-07-01 11:45:00', '2026-07-01 16:00:00',
    '2026-07-02 09:20:00', '2026-07-02 13:00:00', '2026-07-02 17:30:00', '2026-07-02 20:10:00',
    '2026-07-03 08:45:00', '2026-07-03 12:30:00', '2026-07-03 15:50:00',
    '2026-07-04 10:00:00', '2026-07-04 14:20:00', '2026-07-04 18:45:00', '2026-07-04 21:00:00',
    '2026-07-05 09:10:00', '2026-07-05 11:30:00', '2026-07-05 16:20:00',
    '2026-07-06 08:00:00', '2026-07-06 12:45:00', '2026-07-06 15:00:00', '2026-07-06 19:30:00',
    '2026-07-07 10:30:00', '2026-07-07 13:15:00', '2026-07-07 17:00:00',
    '2026-07-08 08:20:00', '2026-07-08 11:00:00', '2026-07-08 14:30:00', '2026-07-08 17:45:00',
];

// Get current max order number
$maxNum = (int)$pdo->query("SELECT MAX(CAST(SUBSTRING(ma_don_hang, 3) AS UNSIGNED)) FROM don_hang WHERE ma_don_hang LIKE 'DH%'")->fetchColumn();
$orderNum = $maxNum + 1;

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

$count = 0;
foreach ($orderDates as $date) {
    $custId  = $customerIds[array_rand($customerIds)];
    $addr    = $addresses[$custId];

    // July orders: mix of statuses (recent, so mostly pending/processing, some completed)
    $pool = ['CHO_XU_LY','CHO_XU_LY','DANG_XU_LY','DANG_XU_LY','HOAN_TAT','HOAN_TAT'];
    $status = $pool[array_rand($pool)];
    $payStatus = ($status === 'HOAN_TAT') ? 'DA_THANH_TOAN' : 'CHUA_THANH_TOAN';

    $lineCount = rand(1, 3);
    shuffle($productIds);
    $chosenPids = array_slice($productIds, 0, $lineCount);

    $tamTinh = 0;
    $lines = [];
    foreach ($chosenPids as $pid) {
        $skuList = $products[$pid];
        $sku = $skuList[array_rand($skuList)];
        $qty = rand(1, 2);
        $sub = $sku[1] * $qty;
        $tamTinh += $sub;
        $lines[] = [$pid, $sku[0], $sku[2], $sku[1], $qty, $sub];
    }
    $tongTien = $tamTinh + 30000;

    $insertOrder->execute([
        'DH' . $orderNum++, $custId, $status, $payStatus,
        $tamTinh, $tongTien,
        $addr[0], $addr[1], $addr[2],
        $date, $date,
    ]);
    $orderId = $pdo->lastInsertId();
    foreach ($lines as $ln) {
        $insertItem->execute([$orderId, $ln[0], $ln[1], $ln[2], $ln[3], $ln[4], $ln[5], $date]);
    }
    $count++;
}

header('Content-Type: text/plain; charset=utf-8');
echo "✓ Inserted $count additional July orders (DH" . ($maxNum + 1) . "–DH" . ($orderNum - 1) . ")\n";
echo "⚠️  Delete this file: public/seed_july.php\n";
