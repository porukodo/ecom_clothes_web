<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Product.php';

final class ProductController
{
  public function danhSach(): void
  {
  $params = [
    'tu_khoa'     => $_GET['tu_khoa'] ?? '',
    'trang'       => $_GET['trang'] ?? 1,
    'gioi_han'    => $_GET['gioi_han'] ?? 10,

    'danh_muc_id' => $_GET['danh_muc_id'] ?? null,
    'kich_co_id'  => $_GET['kich_co_id'] ?? null,

    'gia_tu'      => $_GET['gia_tu'] ?? null,
    'gia_den'     => $_GET['gia_den'] ?? null,
    'sap_xep'     => $_GET['sap_xep'] ?? null,
  ];

  $data = Product::danhSach($params);
  json(['ok' => true] + $data);
  }


  public function chiTiet(int $id): void
  {
    if ($id <= 0) json(['error' => 'ID không hợp lệ'], 400);

    $sp = Product::chiTiet($id);
    if (!$sp) json(['error' => 'Không tìm thấy sản phẩm hoặc sản phẩm không còn bán'], 404);

    json(['ok' => true, 'san_pham' => $sp]);
  }
}
