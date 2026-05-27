<?php
declare(strict_types=1);

require_once __DIR__ . '/../Database.php';

final class Cart
{
  /** Lấy hoặc tạo giỏ hàng cho user */
  public static function layHoacTaoGioHangId(int $nguoi_dung_id): int
  {
    $pdo = Database::pdo();

    $stm = $pdo->prepare("SELECT id FROM gio_hang WHERE nguoi_dung_id = :uid LIMIT 1");
    $stm->execute(['uid' => $nguoi_dung_id]);
    $row = $stm->fetch();

    if ($row && isset($row['id'])) return (int)$row['id'];

    $stm = $pdo->prepare("INSERT INTO gio_hang (nguoi_dung_id) VALUES (:uid)");
    $stm->execute(['uid' => $nguoi_dung_id]);
    return (int)$pdo->lastInsertId();
  }

  /** Xem giỏ hàng */
  public static function xem(int $nguoi_dung_id): array
  {
    $pdo = Database::pdo();
    $gio_hang_id = self::layHoacTaoGioHangId($nguoi_dung_id);

    $sql = "
      SELECT
        ct.id AS chi_tiet_id,
        ct.gio_hang_id,
        ct.san_pham_id,
        ct.sku_id,
        sp.ten_san_pham,
        sp.duong_dan,
        
        -- QUAN TRỌNG: Lấy ảnh từ SKU (đường dẫn thực tế)
        sku.anh_url AS anh_dai_dien_url,
        
        sku.ma_sku,
        sku.kich_co_id,
        sku.mau_sac_id,
        sku.gia_ban AS gia_hien_tai,
        sku.so_luong_ton AS ton_hien_tai,
        sku.trang_thai AS trang_thai_sku,
        
        ct.so_luong,
        ct.don_gia,
        (ct.so_luong * ct.don_gia) AS thanh_tien
      FROM chi_tiet_gio_hang ct
      JOIN sku_san_pham sku ON sku.id = ct.sku_id
      JOIN san_pham sp ON sp.id = ct.san_pham_id
      WHERE ct.gio_hang_id = :gid
      ORDER BY ct.cap_nhat_luc DESC, ct.id DESC
    ";
    $stm = $pdo->prepare($sql);
    $stm->execute(['gid' => $gio_hang_id]);
    $items = $stm->fetchAll();

    $tam_tinh = 0.0;
    foreach ($items as $it) $tam_tinh += (float)$it['thanh_tien'];

    return [
      'gio_hang_id' => $gio_hang_id,
      'items' => $items,
      'tam_tinh' => $tam_tinh,
    ];
  }

  /** Thêm vào giỏ: nếu đã có thì cộng dồn */
  public static function them(int $nguoi_dung_id, int $sku_id, int $so_luong): array
  {
    $pdo = Database::pdo();
    $gio_hang_id = self::layHoacTaoGioHangId($nguoi_dung_id);

    if ($so_luong <= 0) {
      return ['ok' => false, 'code' => 400, 'error' => 'Số lượng phải >= 1'];
    }

    // Lấy SKU + tồn kho + giá + trạng thái + san_pham_id
    $stm = $pdo->prepare("
      SELECT id, san_pham_id, ma_sku, gia_ban, so_luong_ton, trang_thai
      FROM sku_san_pham
      WHERE id = :id
      LIMIT 1
    ");
    $stm->execute(['id' => $sku_id]);
    $sku = $stm->fetch();

    if (!$sku) return ['ok' => false, 'code' => 404, 'error' => 'SKU không tồn tại'];

    if (($sku['trang_thai'] ?? '') !== 'DANG_BAN') {
      return ['ok' => false, 'code' => 403, 'error' => 'SKU không còn bán'];
    }

    $ton = (int)$sku['so_luong_ton'];
    $don_gia = (float)$sku['gia_ban'];
    $san_pham_id = (int)$sku['san_pham_id'];

    // Item hiện có theo (gio_hang_id, sku_id)
    $stm = $pdo->prepare("
      SELECT id, so_luong
      FROM chi_tiet_gio_hang
      WHERE gio_hang_id = :gid AND sku_id = :sku_id
      LIMIT 1
    ");
    $stm->execute(['gid' => $gio_hang_id, 'sku_id' => $sku_id]);
    $ct = $stm->fetch();

    $so_luong_moi = $so_luong;
    if ($ct) $so_luong_moi = (int)$ct['so_luong'] + $so_luong;

    if ($so_luong_moi > $ton) {
      return ['ok' => false, 'code' => 409, 'error' => "Không đủ tồn kho. Chỉ còn $ton sản phẩm."];
    }

    if ($ct) {
      $stm = $pdo->prepare("UPDATE chi_tiet_gio_hang SET so_luong = :qty, don_gia = :price WHERE id = :id");
      $stm->execute(['qty' => $so_luong_moi, 'price' => $don_gia, 'id' => (int)$ct['id']]);
      return ['ok' => true, 'action' => 'UPDATED', 'chi_tiet_id' => (int)$ct['id'], 'so_luong' => $so_luong_moi];
    }

    // Insert: bắt buộc có san_pham_id (vì schema của bạn đang có)
    $stm = $pdo->prepare("
      INSERT INTO chi_tiet_gio_hang (gio_hang_id, san_pham_id, sku_id, so_luong, don_gia)
      VALUES (:gid, :pid, :sku_id, :qty, :price)
    ");
    $stm->execute([
      'gid' => $gio_hang_id,
      'pid' => $san_pham_id,
      'sku_id' => $sku_id,
      'qty' => $so_luong,
      'price' => $don_gia,
    ]);

    return ['ok' => true, 'action' => 'CREATED', 'chi_tiet_id' => (int)$pdo->lastInsertId(), 'so_luong' => $so_luong];
  }


  /** Cập nhật số lượng theo chi_tiet_id */
  public static function capNhat(int $nguoi_dung_id, int $chi_tiet_id, int $so_luong_moi): array
  {
    $pdo = Database::pdo();
    if ($so_luong_moi <= 0) {
      return ['ok' => false, 'code' => 400, 'error' => 'Số lượng phải >= 1'];
    }

    // Item phải thuộc giỏ của user
    $sql = "
      SELECT ct.id, ct.sku_id
      FROM chi_tiet_gio_hang ct
      JOIN gio_hang gh ON gh.id = ct.gio_hang_id
      WHERE ct.id = :ctid AND gh.nguoi_dung_id = :uid
      LIMIT 1
    ";
    $stm = $pdo->prepare($sql);
    $stm->execute(['ctid' => $chi_tiet_id, 'uid' => $nguoi_dung_id]);
    $ct = $stm->fetch();
    if (!$ct) return ['ok' => false, 'code' => 404, 'error' => 'Không tìm thấy item trong giỏ hàng'];

    // Check tồn kho + trạng thái SKU
    $stm = $pdo->prepare("SELECT gia_ban, so_luong_ton, trang_thai FROM sku_san_pham WHERE id = :id LIMIT 1");
    $stm->execute(['id' => (int)$ct['sku_id']]);
    $sku = $stm->fetch();
    if (!$sku) return ['ok' => false, 'code' => 404, 'error' => 'SKU không tồn tại'];

    if (($sku['trang_thai'] ?? '') !== 'DANG_BAN') {
      return ['ok' => false, 'code' => 403, 'error' => 'SKU không còn bán'];
    }

    $ton = (int)$sku['so_luong_ton'];
    if ($so_luong_moi > $ton) {
      return ['ok' => false, 'code' => 409, 'error' => "Không đủ tồn kho. Chỉ còn $ton sản phẩm."];
    }

    $don_gia = (float)$sku['gia_ban'];

    $stm = $pdo->prepare("UPDATE chi_tiet_gio_hang SET so_luong = :qty, don_gia = :price WHERE id = :id");
    $stm->execute(['qty' => $so_luong_moi, 'price' => $don_gia, 'id' => $chi_tiet_id]);

    return ['ok' => true, 'chi_tiet_id' => $chi_tiet_id, 'so_luong' => $so_luong_moi];
  }


  /** Xóa item */
  public static function xoa(int $nguoi_dung_id, int $chi_tiet_id): array
  {
    $pdo = Database::pdo();

    $sql = "
      DELETE ct
      FROM chi_tiet_gio_hang ct
      JOIN gio_hang gh ON gh.id = ct.gio_hang_id
      WHERE ct.id = :ctid AND gh.nguoi_dung_id = :uid
    ";
    $stm = $pdo->prepare($sql);
    $stm->execute(['ctid' => $chi_tiet_id, 'uid' => $nguoi_dung_id]);

    if ($stm->rowCount() <= 0) {
      return ['ok' => false, 'code' => 404, 'error' => 'Không tìm thấy item để xóa'];
    }

    return ['ok' => true, 'deleted' => true, 'chi_tiet_id' => $chi_tiet_id];
  }
}
