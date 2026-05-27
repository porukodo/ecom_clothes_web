<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../Database.php';

final class OrderController
{
  private function requireLogin(): int
  {
    $uid = (int)($_SESSION['nguoi_dung_id'] ?? 0);
    if ($uid <= 0) json(['error' => 'Bạn cần đăng nhập'], 401);
    return $uid;
  }

  // POST /api/don-hang
  public function tao(): void
  {
    $uid = $this->requireLogin();
    $body = doc_json_body();

    $nguoi_nhan = trim((string)($body['nguoi_nhan'] ?? ''));
    $sdt        = trim((string)($body['sdt_nguoi_nhan'] ?? ''));
    $dia_chi    = trim((string)($body['dia_chi_giao_hang'] ?? ''));
    $ghi_chu    = trim((string)($body['ghi_chu'] ?? ''));
    $phi_ship   = (float)($body['phi_van_chuyen'] ?? 30000);
    $giam_gia   = (float)($body['giam_gia'] ?? 0);

    if ($nguoi_nhan === '' || $sdt === '' || $dia_chi === '') {
      json(['error' => 'Vui lòng nhập đầy đủ người nhận / SĐT / địa chỉ'], 400);
    }

    $r = Order::taoTuGioHang($uid, [
      'nguoi_nhan' => $nguoi_nhan,
      'sdt_nguoi_nhan' => $sdt,
      'dia_chi_giao_hang' => $dia_chi,
      'ghi_chu' => $ghi_chu !== '' ? $ghi_chu : null,
      'phi_van_chuyen' => $phi_ship,
      'giam_gia' => $giam_gia,
      'phuong_thuc_thanh_toan' => 'COD',
    ]);

    if (!($r['ok'] ?? false)) {
      json(['error' => $r['error'] ?? 'Lỗi'], (int)($r['code'] ?? 400));
    }

    json(['ok' => true] + $r, 201);
  }

  // POST /api/don-hang/buy-now - Mua ngay từ trang chi tiết
  public function buyNow(): void
  {
      $uid = $this->requireLogin();
      $body = doc_json_body();

      // Thông tin sản phẩm
      $sku_id    = (int)($body['sku_id'] ?? 0);
      $so_luong  = (int)($body['so_luong'] ?? 1);
      
      // Thông tin giao hàng
      $nguoi_nhan = trim((string)($body['nguoi_nhan'] ?? ''));
      $sdt        = trim((string)($body['sdt_nguoi_nhan'] ?? ''));
      $dia_chi    = trim((string)($body['dia_chi_giao_hang'] ?? ''));
      $ghi_chu    = trim((string)($body['ghi_chu'] ?? ''));
      $phi_ship   = (float)($body['phi_van_chuyen'] ?? 30000);
      $giam_gia   = (float)($body['giam_gia'] ?? 0);

      if ($sku_id <= 0 || $so_luong <= 0) {
          json(['error' => 'Thông tin sản phẩm không hợp lệ'], 400);
      }

      if ($nguoi_nhan === '' || $sdt === '' || $dia_chi === '') {
          json(['error' => 'Vui lòng nhập đầy đủ người nhận / SĐT / địa chỉ'], 400);
      }

      $r = Order::taoTrucTiep($uid, $sku_id, $so_luong, [
          'nguoi_nhan' => $nguoi_nhan,
          'sdt_nguoi_nhan' => $sdt,
          'dia_chi_giao_hang' => $dia_chi,
          'ghi_chu' => $ghi_chu !== '' ? $ghi_chu : null,
          'phi_van_chuyen' => $phi_ship,
          'giam_gia' => $giam_gia,
          'phuong_thuc_thanh_toan' => 'COD',
      ]);

      if (!($r['ok'] ?? false)) {
          json(['error' => $r['error'] ?? 'Lỗi'], (int)($r['code'] ?? 400));
      }

      json(['ok' => true] + $r, 201);
  }

  // GET /api/don-hang
  public function danhSach(): void
  {
    $uid = $this->requireLogin();
    $items = Order::danhSachTheoNguoiDung($uid);
    json(['ok' => true, 'items' => $items]);
  }

  // GET /api/don-hang/{id}
  public function chiTiet(int $don_hang_id): void
  {
    $uid = $this->requireLogin();
    $data = Order::chiTietTheoNguoiDung($uid, $don_hang_id);
    if (!($data['ok'] ?? false)) json(['error' => $data['error'] ?? 'Không tìm thấy'], (int)($data['code'] ?? 404));
    json($data);
  }

  public function laySanPhamDeMuaLai(int $don_hang_id): void {
    $uid = $this->requireLogin();
    $pdo = Database::pdo();
    
    $stm = $pdo->prepare("
        SELECT san_pham_id, so_luong, sku_id 
        FROM chi_tiet_don_hang 
        WHERE don_hang_id = :dhid
    ");
    $stm->execute(['dhid' => $don_hang_id]);
    $items = $stm->fetchAll(PDO::FETCH_ASSOC);
    
    json(['ok' => true, 'items' => $items]);
  }

  // --- MỚI: Xử lý yêu cầu hủy đơn ---
  public function yeuCauHuy(int $don_hang_id): void 
  {
      $uid = $this->requireLogin();
      $body = doc_json_body();
      $ly_do = trim((string)($body['ly_do'] ?? ''));

      if (empty($ly_do)) {
          json(['error' => 'Vui lòng cung cấp lý do hủy'], 400);
      }

      $pdo = Database::pdo();
      
      // 1. Kiểm tra đơn hàng có phải của user này và đang ở trạng thái CHO_XU_LY không
      $stmt = $pdo->prepare("SELECT id, trang_thai FROM don_hang WHERE id = ? AND nguoi_dung_id = ?");
      $stmt->execute([$don_hang_id, $uid]);
      $order = $stmt->fetch();

      if (!$order) {
          json(['error' => 'Đơn hàng không tồn tại'], 404);
      }

      if ($order['trang_thai'] !== 'CHO_XU_LY') {
          json(['error' => 'Chỉ có thể hủy đơn hàng khi đang chờ xử lý'], 400);
      }

      // 2. Cập nhật trạng thái
      try {
          $pdo->beginTransaction();

          // Cập nhật trạng thái và lý do
          $stmt = $pdo->prepare("UPDATE don_hang SET trang_thai = 'YEU_CAU_HUY', ly_do_huy = ? WHERE id = ?");
          $stmt->execute([$ly_do, $don_hang_id]);

          // Ghi log lịch sử
          $stmt = $pdo->prepare("INSERT INTO lich_su_trang_thai_don_hang (don_hang_id, tu_trang_thai, den_trang_thai, nguoi_thay_doi_id, ghi_chu) VALUES (?, ?, ?, ?, ?)");
          $stmt->execute([$don_hang_id, 'CHO_XU_LY', 'YEU_CAU_HUY', $uid, "Khách yêu cầu hủy: $ly_do"]);

          $pdo->commit();
          json(['ok' => true, 'message' => 'Đã gửi yêu cầu hủy đơn hàng']);
      } catch (Exception $e) {
          $pdo->rollBack();
          json(['error' => 'Lỗi hệ thống: ' + $e->getMessage()], 500);
      }
  }
}
?>