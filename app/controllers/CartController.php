<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Cart.php';

final class CartController
{
  private function requireLogin(): int
  {
    $uid = (int)($_SESSION['nguoi_dung_id'] ?? 0);
    if ($uid <= 0) json(['error' => 'Bạn cần đăng nhập'], 401);
    return $uid;
  }

  public function xem(): void
  {
    $uid = $this->requireLogin();
    $data = Cart::xem($uid);
    json(['ok' => true] + $data);
  }

  public function them(): void
  {
    $uid = $this->requireLogin();
    $body = doc_json_body();

    $sku_id  = (int)($body['sku_id'] ?? 0);
    $so_luong = (int)($body['so_luong'] ?? 1);

    if ($sku_id <= 0) json(['error' => 'sku_id không hợp lệ'], 400);

    $r = Cart::them($uid, $sku_id, $so_luong);
    if (!($r['ok'] ?? false)) json(['error' => $r['error'] ?? 'Lỗi'], (int)($r['code'] ?? 400));

    json(['ok' => true, 'message' => 'Đã thêm vào giỏ', 'result' => $r], 201);
  }

  public function capNhat(int $chi_tiet_id): void
  {
    $uid = $this->requireLogin();
    $body = doc_json_body();

    $so_luong = (int)($body['so_luong'] ?? 0);
    if ($chi_tiet_id <= 0) json(['error' => 'ID không hợp lệ'], 400);

    $r = Cart::capNhat($uid, $chi_tiet_id, $so_luong);
    if (!($r['ok'] ?? false)) json(['error' => $r['error'] ?? 'Lỗi'], (int)($r['code'] ?? 400));

    json(['ok' => true, 'message' => 'Đã cập nhật', 'result' => $r]);
  }

  public function xoa(int $chi_tiet_id): void
  {
    $uid = $this->requireLogin();
    if ($chi_tiet_id <= 0) json(['error' => 'ID không hợp lệ'], 400);

    $r = Cart::xoa($uid, $chi_tiet_id);
    if (!($r['ok'] ?? false)) json(['error' => $r['error'] ?? 'Lỗi'], (int)($r['code'] ?? 400));

    json(['ok' => true, 'message' => 'Đã xóa', 'result' => $r]);
  }
}
