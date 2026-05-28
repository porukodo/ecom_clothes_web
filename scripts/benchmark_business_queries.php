<?php
/**
 * Business-procedure query benchmark — V0 (plain) vs V2 (AES-256-GCM).
 *
 * Simulates the 10 most common application flows that differ between
 * the two versions, each run N iterations with a warmup pass first.
 *
 * Usage:
 *   php scripts/benchmark_business_queries.php
 *   php scripts/benchmark_business_queries.php --iterations=500
 *
 * Output: formatted comparison table to stdout.
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
require_once $root . '/app/security/PiiFields.php';

// ── CLI args ─────────────────────────────────────────────────────────────────
$iterations = 300;
$warmup     = 30;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--iterations=')) {
        $iterations = max(10, (int) substr($arg, 13));
    }
}

// ── Bootstrap ────────────────────────────────────────────────────────────────
$pdo = Database::pdo();
KeyManager::aesKey(); // warm RSA unwrap once

// ── Anchor data (real non-benchmark records) ─────────────────────────────────
$TEST_USER_ID    = 3;
$TEST_EMAIL      = 'pbui02@gmail.com';
$TEST_ORDER_ID   = 1;
$TEST_ORDER_CODE = 'DH20251220-129EBB';
$TEST_PRODUCT_ID = 1;
$TEST_PAGE_LIMIT = 20;

// ── Helper: decrypt a row's PII fields (V2 path) ─────────────────────────────
function decryptFields(array $row, array $fields): array
{
    foreach ($fields as $f) {
        if (!empty($row[$f])) {
            $row[$f] = EncryptionService::decrypt($row[$f]) ?? $row[$f];
        }
    }
    return $row;
}

// ── Benchmark runner ─────────────────────────────────────────────────────────
function bench(callable $fn, int $iterations, int $warmup): array
{
    for ($i = 0; $i < $warmup; $i++) {
        $fn();
    }

    $times = [];
    for ($i = 0; $i < $iterations; $i++) {
        $start   = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $start) / 1_000_000; // ns → ms
    }

    sort($times);
    $n   = count($times);
    $sum = array_sum($times);

    return [
        'mean'   => $sum / $n,
        'median' => $times[(int) floor($n / 2)],
        'p95'    => $times[(int) floor($n * 0.95)],
        'min'    => $times[0],
        'max'    => $times[$n - 1],
    ];
}

// ─────────────────────────────────────────────────────────────────────────────
// SCENARIOS
// Each scenario runs two lambdas: one for V0 (plain SQL, no decrypt),
// one for V2 (same SQL + PHP-layer AES-256-GCM decryption).
// ─────────────────────────────────────────────────────────────────────────────
$scenarios = [

    // 1. Login — find user by email
    [
        'name' => 'Login: find user by email',
        'tier' => 'Auth',
        'v0'   => function () use ($pdo, $TEST_EMAIL) {
            $st = $pdo->prepare('SELECT id, email, mat_khau_bam, vai_tro, trang_thai
                                   FROM nguoi_dung WHERE email = ? LIMIT 1');
            $st->execute([$TEST_EMAIL]);
            $st->fetch(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_EMAIL) {
            $st = $pdo->prepare('SELECT id, email, mat_khau_bam, ho_ten, so_dien_thoai,
                                        ngay_sinh, vai_tro, trang_thai
                                   FROM nguoi_dung WHERE email = ? LIMIT 1');
            $st->execute([$TEST_EMAIL]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                decryptFields($row, PiiFields::USER);
            }
        },
    ],

    // 2. Profile page — fetch single user profile
    [
        'name' => 'Profile: load user by ID',
        'tier' => 'Customer',
        'v0'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT * FROM nguoi_dung WHERE id = ? LIMIT 1');
            $st->execute([$TEST_USER_ID]);
            $st->fetch(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT * FROM nguoi_dung WHERE id = ? LIMIT 1');
            $st->execute([$TEST_USER_ID]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                decryptFields($row, PiiFields::USER);
            }
        },
    ],

    // 3. Address book — fetch all addresses for a user
    [
        'name' => 'Address book: list by user',
        'tier' => 'Customer',
        'v0'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT * FROM dia_chi
                                  WHERE nguoi_dung_id = ?
                                  ORDER BY mac_dinh DESC, id DESC');
            $st->execute([$TEST_USER_ID]);
            $st->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT * FROM dia_chi
                                  WHERE nguoi_dung_id = ?
                                  ORDER BY mac_dinh DESC, id DESC');
            $st->execute([$TEST_USER_ID]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                decryptFields($row, PiiFields::ADDRESS);
            }
        },
    ],

    // 4. Shop page — paginated product listing (no PII, same both versions)
    [
        'name' => 'Shop: paginated product list',
        'tier' => 'Catalog',
        'v0'   => function () use ($pdo, $TEST_PAGE_LIMIT) {
            $st = $pdo->prepare('SELECT s.id, s.ten_san_pham, s.mo_ta, s.gia_ban,
                                        s.duong_dan, s.anh_dai_dien_url, d.ten_danh_muc
                                   FROM san_pham s
                                   JOIN danh_muc_san_pham d ON d.id = s.danh_muc_id
                                  WHERE s.trang_thai = "HOAT_DONG"
                                  ORDER BY s.id DESC
                                  LIMIT ?');
            $st->execute([$TEST_PAGE_LIMIT]);
            $st->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_PAGE_LIMIT) {
            // Identical — product data carries no PII
            $st = $pdo->prepare('SELECT s.id, s.ten_san_pham, s.mo_ta, s.gia_ban,
                                        s.duong_dan, s.anh_dai_dien_url, d.ten_danh_muc
                                   FROM san_pham s
                                   JOIN danh_muc_san_pham d ON d.id = s.danh_muc_id
                                  WHERE s.trang_thai = "HOAT_DONG"
                                  ORDER BY s.id DESC
                                  LIMIT ?');
            $st->execute([$TEST_PAGE_LIMIT]);
            $st->fetchAll(PDO::FETCH_ASSOC);
        },
    ],

    // 5. Product detail — single product + SKUs + images
    [
        'name' => 'Product detail: fetch with SKUs',
        'tier' => 'Catalog',
        'v0'   => function () use ($pdo, $TEST_PRODUCT_ID) {
            $st = $pdo->prepare('SELECT s.*, d.ten_danh_muc FROM san_pham s
                                   JOIN danh_muc_san_pham d ON d.id = s.danh_muc_id
                                  WHERE s.id = ? LIMIT 1');
            $st->execute([$TEST_PRODUCT_ID]);
            $st->fetch(PDO::FETCH_ASSOC);
            $st2 = $pdo->prepare('SELECT sk.*, ms.ten_mau, ms.ma_mau, kc.ten_kich_co
                                     FROM sku_san_pham sk
                                     JOIN mau_sac ms ON ms.id = sk.mau_sac_id
                                     JOIN kich_co kc ON kc.id = sk.kich_co_id
                                    WHERE sk.san_pham_id = ? AND sk.trang_thai = "HOAT_DONG"');
            $st2->execute([$TEST_PRODUCT_ID]);
            $st2->fetchAll(PDO::FETCH_ASSOC);
            $st3 = $pdo->prepare('SELECT url_anh FROM anh_san_pham
                                   WHERE san_pham_id = ? ORDER BY thu_tu_hien_thi');
            $st3->execute([$TEST_PRODUCT_ID]);
            $st3->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_PRODUCT_ID) {
            // Identical — no PII in product data
            $st = $pdo->prepare('SELECT s.*, d.ten_danh_muc FROM san_pham s
                                   JOIN danh_muc_san_pham d ON d.id = s.danh_muc_id
                                  WHERE s.id = ? LIMIT 1');
            $st->execute([$TEST_PRODUCT_ID]);
            $st->fetch(PDO::FETCH_ASSOC);
            $st2 = $pdo->prepare('SELECT sk.*, ms.ten_mau, ms.ma_mau, kc.ten_kich_co
                                     FROM sku_san_pham sk
                                     JOIN mau_sac ms ON ms.id = sk.mau_sac_id
                                     JOIN kich_co kc ON kc.id = sk.kich_co_id
                                    WHERE sk.san_pham_id = ? AND sk.trang_thai = "HOAT_DONG"');
            $st2->execute([$TEST_PRODUCT_ID]);
            $st2->fetchAll(PDO::FETCH_ASSOC);
            $st3 = $pdo->prepare('SELECT url_anh FROM anh_san_pham
                                   WHERE san_pham_id = ? ORDER BY thu_tu_hien_thi');
            $st3->execute([$TEST_PRODUCT_ID]);
            $st3->fetchAll(PDO::FETCH_ASSOC);
        },
    ],

    // 6. Cart page — fetch cart + items for a user
    [
        'name' => 'Cart: load items for user',
        'tier' => 'Cart',
        'v0'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT id FROM gio_hang WHERE nguoi_dung_id = ? LIMIT 1');
            $st->execute([$TEST_USER_ID]);
            $cart = $st->fetch(PDO::FETCH_ASSOC);
            if ($cart) {
                $st2 = $pdo->prepare('SELECT gc.*, sk.gia_ban, sk.so_luong_ton,
                                             s.ten_san_pham, s.duong_dan,
                                             ms.ten_mau, ms.ma_mau, kc.ten_kich_co,
                                             sk.anh_url AS anh
                                        FROM chi_tiet_gio_hang gc
                                        JOIN sku_san_pham sk ON sk.id = gc.sku_id
                                        JOIN san_pham s      ON s.id  = sk.san_pham_id
                                        JOIN mau_sac ms      ON ms.id = sk.mau_sac_id
                                        JOIN kich_co kc      ON kc.id = sk.kich_co_id
                                       WHERE gc.gio_hang_id = ?');
                $st2->execute([$cart['id']]);
                $st2->fetchAll(PDO::FETCH_ASSOC);
            }
        },
        'v2'   => function () use ($pdo, $TEST_USER_ID) {
            // Identical — cart contains no PII
            $st = $pdo->prepare('SELECT id FROM gio_hang WHERE nguoi_dung_id = ? LIMIT 1');
            $st->execute([$TEST_USER_ID]);
            $cart = $st->fetch(PDO::FETCH_ASSOC);
            if ($cart) {
                $st2 = $pdo->prepare('SELECT gc.*, sk.gia_ban, sk.so_luong_ton,
                                             s.ten_san_pham, s.duong_dan,
                                             ms.ten_mau, ms.ma_mau, kc.ten_kich_co,
                                             sk.anh_url AS anh
                                        FROM chi_tiet_gio_hang gc
                                        JOIN sku_san_pham sk ON sk.id = gc.sku_id
                                        JOIN san_pham s      ON s.id  = sk.san_pham_id
                                        JOIN mau_sac ms      ON ms.id = sk.mau_sac_id
                                        JOIN kich_co kc      ON kc.id = sk.kich_co_id
                                       WHERE gc.gio_hang_id = ?');
                $st2->execute([$cart['id']]);
                $st2->fetchAll(PDO::FETCH_ASSOC);
            }
        },
    ],

    // 7. My Orders — fetch order list for a customer
    [
        'name' => 'My Orders: list for user',
        'tier' => 'Order',
        'v0'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT id, ma_don_hang, trang_thai, tong_tien,
                                        nguoi_nhan, sdt_nguoi_nhan, tao_luc
                                   FROM don_hang
                                  WHERE nguoi_dung_id = ?
                                  ORDER BY tao_luc DESC
                                  LIMIT 10');
            $st->execute([$TEST_USER_ID]);
            $st->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_USER_ID) {
            $st = $pdo->prepare('SELECT id, ma_don_hang, trang_thai, tong_tien,
                                        nguoi_nhan, sdt_nguoi_nhan, tao_luc
                                   FROM don_hang
                                  WHERE nguoi_dung_id = ?
                                  ORDER BY tao_luc DESC
                                  LIMIT 10');
            $st->execute([$TEST_USER_ID]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                decryptFields($row, ['nguoi_nhan', 'sdt_nguoi_nhan']);
            }
        },
    ],

    // 8. Order detail — full order + items + status history
    [
        'name' => 'Order detail: single order + items',
        'tier' => 'Order',
        'v0'   => function () use ($pdo, $TEST_ORDER_ID) {
            $st = $pdo->prepare('SELECT * FROM don_hang WHERE id = ? LIMIT 1');
            $st->execute([$TEST_ORDER_ID]);
            $st->fetch(PDO::FETCH_ASSOC);
            $st2 = $pdo->prepare('SELECT cd.*, s.ten_san_pham, ms.ten_mau, ms.ma_mau,
                                         kc.ten_kich_co, sk.anh_url AS anh
                                    FROM chi_tiet_don_hang cd
                                    JOIN sku_san_pham sk ON sk.id = cd.sku_id
                                    JOIN san_pham s      ON s.id  = sk.san_pham_id
                                    JOIN mau_sac ms      ON ms.id = sk.mau_sac_id
                                    JOIN kich_co kc      ON kc.id = sk.kich_co_id
                                   WHERE cd.don_hang_id = ?');
            $st2->execute([$TEST_ORDER_ID]);
            $st2->fetchAll(PDO::FETCH_ASSOC);
            $st3 = $pdo->prepare('SELECT * FROM lich_su_trang_thai_don_hang
                                   WHERE don_hang_id = ? ORDER BY tao_luc DESC');
            $st3->execute([$TEST_ORDER_ID]);
            $st3->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_ORDER_ID) {
            $st = $pdo->prepare('SELECT * FROM don_hang WHERE id = ? LIMIT 1');
            $st->execute([$TEST_ORDER_ID]);
            $order = $st->fetch(PDO::FETCH_ASSOC);
            if ($order) {
                decryptFields($order, PiiFields::ORDER);
            }
            $st2 = $pdo->prepare('SELECT cd.*, s.ten_san_pham, ms.ten_mau, ms.ma_mau,
                                         kc.ten_kich_co, sk.anh_url AS anh
                                    FROM chi_tiet_don_hang cd
                                    JOIN sku_san_pham sk ON sk.id = cd.sku_id
                                    JOIN san_pham s      ON s.id  = sk.san_pham_id
                                    JOIN mau_sac ms      ON ms.id = sk.mau_sac_id
                                    JOIN kich_co kc      ON kc.id = sk.kich_co_id
                                   WHERE cd.don_hang_id = ?');
            $st2->execute([$TEST_ORDER_ID]);
            $st2->fetchAll(PDO::FETCH_ASSOC);
            $st3 = $pdo->prepare('SELECT * FROM lich_su_trang_thai_don_hang
                                   WHERE don_hang_id = ? ORDER BY tao_luc DESC');
            $st3->execute([$TEST_ORDER_ID]);
            $st3->fetchAll(PDO::FETCH_ASSOC);
        },
    ],

    // 9. Admin: paginated user list
    [
        'name' => 'Admin: paginated user list (20/page)',
        'tier' => 'Admin',
        'v0'   => function () use ($pdo, $TEST_PAGE_LIMIT) {
            $st = $pdo->prepare('SELECT id, email, ho_ten, so_dien_thoai,
                                        vai_tro, trang_thai, tao_luc
                                   FROM nguoi_dung
                                  ORDER BY id DESC
                                  LIMIT ?');
            $st->execute([$TEST_PAGE_LIMIT]);
            $st->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_PAGE_LIMIT) {
            $st = $pdo->prepare('SELECT id, email, ho_ten, so_dien_thoai,
                                        vai_tro, trang_thai, tao_luc
                                   FROM nguoi_dung
                                  ORDER BY id DESC
                                  LIMIT ?');
            $st->execute([$TEST_PAGE_LIMIT]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                decryptFields($row, ['ho_ten', 'so_dien_thoai']);
            }
        },
    ],

    // 10. Admin: paginated order list with recipient info
    [
        'name' => 'Admin: paginated order list (20/page)',
        'tier' => 'Admin',
        'v0'   => function () use ($pdo, $TEST_PAGE_LIMIT) {
            $st = $pdo->prepare('SELECT d.id, d.ma_don_hang, d.trang_thai, d.tong_tien,
                                        d.nguoi_nhan, d.sdt_nguoi_nhan,
                                        d.phuong_thuc_thanh_toan, d.tao_luc,
                                        n.email
                                   FROM don_hang d
                                   JOIN nguoi_dung n ON n.id = d.nguoi_dung_id
                                  ORDER BY d.id DESC
                                  LIMIT ?');
            $st->execute([$TEST_PAGE_LIMIT]);
            $st->fetchAll(PDO::FETCH_ASSOC);
        },
        'v2'   => function () use ($pdo, $TEST_PAGE_LIMIT) {
            $st = $pdo->prepare('SELECT d.id, d.ma_don_hang, d.trang_thai, d.tong_tien,
                                        d.nguoi_nhan, d.sdt_nguoi_nhan,
                                        d.phuong_thuc_thanh_toan, d.tao_luc,
                                        n.email
                                   FROM don_hang d
                                   JOIN nguoi_dung n ON n.id = d.nguoi_dung_id
                                  ORDER BY d.id DESC
                                  LIMIT ?');
            $st->execute([$TEST_PAGE_LIMIT]);
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$row) {
                decryptFields($row, ['nguoi_nhan', 'sdt_nguoi_nhan']);
            }
        },
    ],
];

// ── Run all scenarios ─────────────────────────────────────────────────────────
$results = [];
$totalScenarios = count($scenarios);
echo "\n━━━ Business-Query Benchmark: V0 (plain) vs V2 (AES-256-GCM) ━━━\n";
echo "    {$iterations} iterations · {$warmup} warmup · MySQL live DB\n\n";

foreach ($scenarios as $i => $sc) {
    $n = $i + 1;
    printf("  [%2d/%2d] Running: %s … ", $n, $totalScenarios, $sc['name']);
    $r0 = bench($sc['v0'], $iterations, $warmup);
    $r2 = bench($sc['v2'], $iterations, $warmup);
    $results[] = [
        'name'   => $sc['name'],
        'tier'   => $sc['tier'],
        'v0'     => $r0,
        'v2'     => $r2,
    ];
    printf("done (V0 mean=%.3f ms, V2 mean=%.3f ms)\n", $r0['mean'], $r2['mean']);
}

// ── Print comparison table ────────────────────────────────────────────────────
$divider = str_repeat('─', 115);
$header  = sprintf(
    "\n%-40s %-9s %9s %9s %9s   %9s %9s %9s   %8s %8s",
    'Business Query', 'Tier',
    'V0 mean', 'V0 med', 'V0 p95',
    'V2 mean', 'V2 med', 'V2 p95',
    'Δ mean', 'Overhead'
);

echo "\n\n";
echo "┌" . str_repeat("─", 113) . "┐\n";
echo "│" . str_pad("  V0 vs V2 — Business Query Performance Comparison ({$iterations} iterations per query, MySQL live DB)", 113) . "│\n";
echo "├" . str_repeat("─", 113) . "┤\n";
echo "│" . str_pad("  All times in milliseconds (ms). Lower is better.", 113) . "│\n";
echo "└" . str_repeat("─", 113) . "┘\n";
echo "\n";

printf(
    "%-40s %-9s %9s %9s %9s   %9s %9s %9s   %9s %9s\n",
    'Business Query', 'Tier',
    'V0 mean', 'V0 med', 'V0 p95',
    'V2 mean', 'V2 med', 'V2 p95',
    'Δ mean', 'Overhead'
);
echo $divider . "\n";

$piiTiers = ['Auth', 'Customer', 'Order', 'Admin']; // tiers affected by encryption

foreach ($results as $r) {
    $v0  = $r['v0'];
    $v2  = $r['v2'];
    $delta   = $v2['mean'] - $v0['mean'];
    $overhead = $v0['mean'] > 0 ? (($v2['mean'] / $v0['mean']) - 1) * 100 : 0;

    // Marker: only PII-touching tiers have real overhead
    $hasPii = in_array($r['tier'], $piiTiers, true);
    $ohStr  = $hasPii
        ? sprintf('%+.1f%%', $overhead)
        : '  —  '; // catalog/cart: overhead is noise

    printf(
        "%-40s %-9s %9.4f %9.4f %9.4f   %9.4f %9.4f %9.4f   %+9.4f %9s\n",
        substr($r['name'], 0, 40),
        $r['tier'],
        $v0['mean'], $v0['median'], $v0['p95'],
        $v2['mean'], $v2['median'], $v2['p95'],
        $delta,
        $ohStr
    );
}

echo $divider . "\n";

// Summary row: average overhead across PII-touching scenarios
$piiResults = array_filter($results, fn($r) => in_array($r['tier'], $piiTiers, true));
$avgV0  = array_sum(array_column(array_column($piiResults, 'v0'), 'mean')) / count($piiResults);
$avgV2  = array_sum(array_column(array_column($piiResults, 'v2'), 'mean')) / count($piiResults);
$avgOh  = (($avgV2 / $avgV0) - 1) * 100;

printf(
    "%-40s %-9s %9.4f %9s %9s   %9.4f %9s %9s   %+9.4f %+9.1f%%\n",
    'AVG (PII-touching scenarios only)', '',
    $avgV0, '', '',
    $avgV2, '', '',
    $avgV2 - $avgV0,
    $avgOh
);
echo $divider . "\n";

// ── Legend ────────────────────────────────────────────────────────────────────
echo "\n";
echo "LEGEND\n";
echo "  V0      Plain PHP/MySQL — no encryption (original PTUD_Final baseline)\n";
echo "  V2      AES-256-GCM field encryption + RSA-2048 OAEP key wrap (current branch)\n";
echo "  Δ mean  V2 mean − V0 mean (positive = V2 is slower)\n";
echo "  Overhead% = (V2/V0 − 1) × 100  [shown only for queries that decrypt PII]\n";
echo "  —       No PII decryption in this flow; overhead is within measurement noise\n";
echo "\n";
echo "TIERS\n";
echo "  Auth     Login flow (email lookup — V2 also decrypts name/phone/dob)\n";
echo "  Customer My-profile & address-book pages\n";
echo "  Catalog  Shop / product-detail — no PII involved\n";
echo "  Cart     Cart page — no PII involved\n";
echo "  Order    My-orders list & order-detail page\n";
echo "  Admin    Admin user list & order list (20 rows, each row decrypted in V2)\n";
echo "\n";
