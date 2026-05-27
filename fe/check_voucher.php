<?php
// File: fe/checkvoucher.php

// Gọi file Database
require_once __DIR__ . '/../app/Database.php';

// --- [QUAN TRỌNG] XÓA DÒNG "use app\Database;" ĐI --- 
// Vì file Database.php của bạn không có namespace 'app'

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$code = $input['code'] ?? '';
$total_cart = $input['total'] ?? 0;

if (empty($code)) {
    echo json_encode(['status' => false, 'message' => 'Vui lòng nhập mã giảm giá']);
    exit;
}

try {
    // Gọi trực tiếp class Database (không cần namespace)
    $pdo = Database::pdo();
    
    if (!$pdo) {
        throw new Exception('Lỗi kết nối cơ sở dữ liệu');
    }

    // --- Các bước kiểm tra như cũ ---
    $stmt = $pdo->prepare("SELECT * FROM khuyen_mai WHERE ma_khuyen_mai = ?");
    $stmt->execute([$code]);
    $voucher = $stmt->fetch();

    if (!$voucher) {
        throw new Exception('Mã giảm giá không tồn tại');
    }

    if ($voucher['trang_thai'] !== 'active') {
        throw new Exception('Mã giảm giá này đang bị khóa');
    }

    $current_date = date('Y-m-d H:i:s');
    $start = $voucher['ngay_bat_dau'] . ' ' . $voucher['gio_bat_dau'];
    $end = $voucher['ngay_ket_thuc'] . ' ' . $voucher['gio_ket_thuc'];

    if ($current_date < $start) {
        throw new Exception('Chương trình chưa bắt đầu');
    }

    if ($current_date > $end) {
        throw new Exception('Mã giảm giá đã hết hạn');
    }

    // Sửa lỗi so sánh đơn tối thiểu (ép kiểu float để tránh lỗi chuỗi)
    if (floatval($total_cart) < floatval($voucher['don_toi_thieu'])) {
        throw new Exception('Đơn hàng cần tối thiểu ' . number_format($voucher['don_toi_thieu']) . 'đ');
    }

    // Tính toán
    $discount = 0;
    $desc = "";
    
    if ($voucher['loai_giam_gia'] == 'fixed') {
        $discount = floatval($voucher['gia_tri_giam']);
        $desc = "Giảm " . number_format($discount) . "đ";
    } else {
        $percent = floatval($voucher['gia_tri_giam']);
        $discount = ($total_cart * $percent) / 100;
        
        if ($voucher['giam_toi_da'] > 0 && $discount > $voucher['giam_toi_da']) {
            $discount = floatval($voucher['giam_toi_da']);
        }
        $desc = "Giảm " . $percent . "%";
    }

    if ($discount > $total_cart) {
        $discount = $total_cart;
    }

    echo json_encode([
        'status' => true,
        'message' => 'Áp dụng mã thành công!',
        'discount' => $discount,
        'code' => $code,
        'desc' => $desc
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}
?>