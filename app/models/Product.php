<?php
declare(strict_types=1);

require_once __DIR__ . '/../Database.php';

final class Product
{
  /**
   * Danh sách sản phẩm (public)
   */
  public static function danhSach(array $params): array
  {
    $pdo = Database::pdo();
    $tu_khoa     = trim((string)($params['tu_khoa'] ?? ''));
    $danh_muc_id = $params['danh_muc_id'] ?? null;
    $kich_co_id  = $params['kich_co_id'] ?? null;

    $gia_tu      = $params['gia_tu'] ?? null;
    $gia_den     = $params['gia_den'] ?? null;
    $sap_xep     = (string)($params['sap_xep'] ?? '');

    $where = ["sp.trang_thai = 'DANG_BAN'"];
    $bind  = [];

    if ($tu_khoa !== '') {
      $where[] = "sp.ten_san_pham LIKE :tu_khoa";
      $bind['tu_khoa'] = '%' . $tu_khoa . '%';
    }

    if ($danh_muc_id !== null && $danh_muc_id !== '') {
      $where[] = "sp.danh_muc_id = :danh_muc_id";
      $bind['danh_muc_id'] = (int)$danh_muc_id;
    }

    if ($kich_co_id !== null && $kich_co_id !== '') {
      $where[] = "EXISTS (
        SELECT 1 FROM sku_san_pham s2
        WHERE s2.san_pham_id = sp.id 
          AND s2.trang_thai = 'DANG_BAN'
          AND s2.kich_co_id = :kich_co_id
      )";
      $bind['kich_co_id'] = (int)$kich_co_id;
    }

    /**
     * Giá hiệu lực = min giá SKU đang bán nếu có, fallback sp.gia_ban
     */
    $effectivePriceExpr = "COALESCE(
      (SELECT MIN(s3.gia_ban) FROM sku_san_pham s3 WHERE s3.san_pham_id = sp.id AND s3.trang_thai='DANG_BAN'),
      sp.gia_ban
    )";

    if ($gia_tu !== null && $gia_tu !== '') {
      $where[] = "$effectivePriceExpr >= :gia_tu";
      $bind['gia_tu'] = (float)$gia_tu;
    }
    if ($gia_den !== null && $gia_den !== '') {
      $where[] = "$effectivePriceExpr <= :gia_den";
      $bind['gia_den'] = (float)$gia_den;
    }

    $whereSql = implode(' AND ', $where);

    // paging
    $trang = max(1, (int)($params['trang'] ?? 1));
    $gioi_han = (int)($params['gioi_han'] ?? 10);
    $gioi_han = min(max($gioi_han, 1), 50);
    $offset = ($trang - 1) * $gioi_han;

    // COUNT
    $sqlCount = "
      SELECT COUNT(DISTINCT sp.id) AS tong
      FROM san_pham sp
      WHERE $whereSql
    ";
    $stm = $pdo->prepare($sqlCount);
    $stm->execute($bind);
    $tong = (int)($stm->fetch()['tong'] ?? 0);

    // ORDER BY
    $orderSql = "sp.tao_luc DESC, sp.id DESC";
    if ($sap_xep === 'gia_tang') $orderSql = "$effectivePriceExpr ASC, sp.id DESC";
    if ($sap_xep === 'gia_giam') $orderSql = "$effectivePriceExpr DESC, sp.id DESC";
    if ($sap_xep === 'moi_nhat') $orderSql = "sp.tao_luc DESC, sp.id DESC";

    $sql = "
      SELECT
      sp.id,
      sp.danh_muc_id,
      dm.ten_danh_muc,
      dm.duong_dan AS duong_dan_danh_muc,
      sp.ten_san_pham,
      sp.duong_dan,
      sp.mo_ta,
      $effectivePriceExpr AS gia_ban,
      sp.trang_thai,
      COALESCE(asp.url_anh, sp.anh_dai_dien_url) AS anh_dai_dien_url,
      sp.tao_luc,
      sp.cap_nhat_luc
      FROM san_pham sp
      LEFT JOIN danh_muc_san_pham dm ON dm.id = sp.danh_muc_id
      LEFT JOIN anh_san_pham asp ON asp.id = sp.anh_dai_dien_url
      WHERE $whereSql
      ORDER BY $orderSql
      LIMIT :limit OFFSET :offset
    ";

    $stm = $pdo->prepare($sql);
    foreach ($bind as $k => $v) $stm->bindValue(':' . $k, $v);
    $stm->bindValue(':limit', $gioi_han, PDO::PARAM_INT);
    $stm->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stm->execute();
    $items = $stm->fetchAll();

    return [
      'items' => $items,
      'paging' => [
        'trang' => $trang,
        'gioi_han' => $gioi_han,
        'tong' => $tong,
        'tong_trang' => (int)ceil($tong / $gioi_han),
      ],
      'filters' => [
        'tu_khoa' => $tu_khoa,
        'danh_muc_id' => ($danh_muc_id === null || $danh_muc_id === '') ? null : (int)$danh_muc_id,
        'kich_co_id' => ($kich_co_id === null || $kich_co_id === '') ? null : (int)$kich_co_id,
        'gia_tu' => ($gia_tu === null || $gia_tu === '') ? null : (float)$gia_tu,
        'gia_den' => ($gia_den === null || $gia_den === '') ? null : (float)$gia_den,
        'sap_xep' => ($sap_xep === '') ? null : $sap_xep,
      ],
    ];
  }

  /**
   * Chi tiết sản phẩm (public)
   */
  public static function chiTiet(int $id): ?array
  {
    $pdo = Database::pdo();

    // CHI TIẾT - ĐÃ XÓA thong_tin_sp VÀ huong_dan_bao_quan
    $sql = "
      SELECT
        sp.id,
        sp.danh_muc_id,
        dm.ten_danh_muc,
        dm.duong_dan AS duong_dan_danh_muc,
        sp.ten_san_pham,
        sp.duong_dan,
        sp.mo_ta,
        sp.gia_ban,
        sp.so_luong_ton,
        sp.trang_thai,
        COALESCE(asp.url_anh, sp.anh_dai_dien_url) AS anh_dai_dien_url,
        sp.tao_luc,
        sp.cap_nhat_luc
      FROM san_pham sp
      LEFT JOIN danh_muc_san_pham dm ON dm.id = sp.danh_muc_id
      LEFT JOIN anh_san_pham asp ON asp.id = sp.anh_dai_dien_url
      WHERE sp.id = :id
      LIMIT 1
    ";
    $stm = $pdo->prepare($sql);
    $stm->execute(['id' => $id]);
    $row = $stm->fetch();

    if (!$row) return null;

    // Với public, chỉ trả về nếu đang bán
    if (($row['trang_thai'] ?? '') !== 'DANG_BAN') return null;

    $sqlVar = "
      SELECT
        sku.id,
        sku.ma_sku,
        sku.gia_ban,
        sku.so_luong_ton,
        sku.kich_co_id,
        kc.ten_kich_co,
        sku.mau_sac_id,
        sku.anh_url,
        ms.ten_mau,
        ms.ma_mau
      FROM sku_san_pham sku
      LEFT JOIN kich_co kc ON kc.id = sku.kich_co_id
      LEFT JOIN mau_sac ms ON ms.id = sku.mau_sac_id
      WHERE sku.san_pham_id = :id
        AND sku.trang_thai = 'DANG_BAN'
      ORDER BY kc.thu_tu ASC, ms.id ASC, sku.id ASC
    ";
    $stmV = $pdo->prepare($sqlVar);
    $stmV->execute(['id' => $id]);
    $variants = $stmV->fetchAll();

    $row['variants'] = $variants;

    // unique sizes/colors cho FE
    $sizes = [];
    $colors = [];
    foreach ($variants as $v) {
      if ($v['kich_co_id']) $sizes[$v['kich_co_id']] = ['id'=>(int)$v['kich_co_id'], 'ten'=>$v['ten_kich_co']];
      if ($v['mau_sac_id']) $colors[$v['mau_sac_id']] = ['id'=>(int)$v['mau_sac_id'], 'ten'=>$v['ten_mau'], 'ma'=>$v['ma_mau']];
    }
    $row['sizes'] = array_values($sizes);
    $row['colors'] = array_values($colors);

    // load thêm ảnh phụ
    $sqlImgs = "SELECT id, url_anh, thu_tu_hien_thi FROM anh_san_pham WHERE san_pham_id = :id ORDER BY thu_tu_hien_thi ASC, id ASC";
    $stm2 = $pdo->prepare($sqlImgs);
    $stm2->execute(['id' => $id]);
    $row['anh_phu'] = $stm2->fetchAll();

    return $row;
  }
}