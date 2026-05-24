<?php
/**
 * Query performance benchmark — measures SQL-only vs SQL + AES-256-GCM overhead.
 *
 * Usage:
 *   php scripts/run_query_benchmark.php
 *   php scripts/run_query_benchmark.php --iterations=500
 *
 * Output: public/benchmark_report.html  (open in browser)
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
foreach ($argv ?? [] as $arg) {
    if (str_starts_with($arg, '--iterations=')) {
        $iterations = max(50, (int) substr($arg, 13));
    }
}

// ── Validate prerequisites ───────────────────────────────────────────────────
if (!KeyManager::isEnabled()) {
    echo "⚠  ENCRYPTION_ENABLED=false — benchmark sẽ không có overhead mã hóa.\n";
    echo "   Chạy php scripts/generate_encryption_keys.php trước để có kết quả có ý nghĩa.\n\n";
}

$pdo = Database::pdo();

$userIds = $pdo
    ->query("SELECT id FROM nguoi_dung WHERE email LIKE 'bm%@bench.local' ORDER BY id LIMIT 4000")
    ->fetchAll(PDO::FETCH_COLUMN);

if (count($userIds) < 100) {
    echo "✗ Chưa đủ dữ liệu benchmark. Chạy: php scripts/seed_test_data.php\n";
    exit(1);
}

$benchUserCount = count($userIds);
$sampleIds      = array_slice($userIds, 0, 30); // Rotate through 30 IDs for single-row queries

echo "━━━ Query Performance Benchmark ━━━\n";
echo "Người dùng benchmark: {$benchUserCount}\n";
echo "Số lần lặp / query:   {$iterations} (warmup: {$warmup})\n";
echo "Mã hóa:               " . (KeyManager::isEnabled() ? 'BẬT (AES-256-GCM + RSA-2048)' : 'TẮT') . "\n\n";

// ── Benchmark helper ─────────────────────────────────────────────────────────
function runBench(string $name, callable $fn, int $n, int $w): array
{
    for ($i = 0; $i < $w; $i++) {
        $fn();
    }
    $times = [];
    for ($i = 0; $i < $n; $i++) {
        $t       = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $t) / 1e6; // nanoseconds → ms
    }
    sort($times);
    $cnt = count($times);
    $sum = array_sum($times);

    return [
        'name'   => $name,
        'n'      => $cnt,
        'mean'   => round($sum / $cnt, 4),
        'median' => round($times[(int) ($cnt / 2)], 4),
        'p95'    => round($times[(int) ($cnt * 0.95)], 4),
        'min'    => round($times[0], 4),
        'max'    => round($times[$cnt - 1], 4),
    ];
}

function decryptRow(array $row, array $fields): array
{
    return EncryptionService::decryptFields($row, $fields);
}

$UP = PiiFields::USER;    // ['ho_ten','so_dien_thoai','ngay_sinh']
$AP = PiiFields::ADDRESS; // ['ten_nguoi_nhan','so_dien_thoai','tinh_thanh','quan_huyen','phuong_xa','dia_chi_cu_the']

// ── Q1: Đăng nhập — SELECT WHERE email (email is plaintext) ──────────────────
echo "Q1 Đăng nhập...\n";
$loginEmail = "bm1@bench.local";

$q1sql = runBench('Q1 — SQL thuần', function () use ($pdo, $loginEmail) {
    $st = $pdo->prepare("SELECT * FROM nguoi_dung WHERE email = ? LIMIT 1");
    $st->execute([$loginEmail]);
    $st->fetch(PDO::FETCH_ASSOC);
}, $iterations, $warmup);

$q1app = runBench('Q1 — Có mã hóa', function () use ($pdo, $loginEmail, $UP) {
    $st = $pdo->prepare("SELECT * FROM nguoi_dung WHERE email = ? LIMIT 1");
    $st->execute([$loginEmail]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        decryptRow($row, $UP);
    }
}, $iterations, $warmup);

// ── Q2: Trang cá nhân — SELECT WHERE id (1 hàng, 3 trường PII) ───────────────
echo "Q2 Trang cá nhân...\n";
$q2sql = runBench('Q2 — SQL thuần', function () use ($pdo, $sampleIds) {
    static $i = 0;
    $st = $pdo->prepare("SELECT * FROM nguoi_dung WHERE id = ? LIMIT 1");
    $st->execute([$sampleIds[$i++ % 30]]);
    $st->fetch(PDO::FETCH_ASSOC);
}, $iterations, $warmup);

$q2app = runBench('Q2 — Có mã hóa', function () use ($pdo, $sampleIds, $UP) {
    static $i = 0;
    $st = $pdo->prepare("SELECT * FROM nguoi_dung WHERE id = ? LIMIT 1");
    $st->execute([$sampleIds[$i++ % 30]]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        decryptRow($row, $UP);
    }
}, $iterations, $warmup);

// ── Q3: Danh sách khách hàng (Admin) — 50 hàng, 3 trường PII × 50 ────────────
echo "Q3 Danh sách khách hàng (50 dòng)...\n";
$q3sql = runBench('Q3 — SQL thuần', function () use ($pdo) {
    $pdo->query("SELECT * FROM nguoi_dung ORDER BY id DESC LIMIT 50")
        ->fetchAll(PDO::FETCH_ASSOC);
}, $iterations, $warmup);

$q3app = runBench('Q3 — Có mã hóa', function () use ($pdo, $UP) {
    $rows = $pdo->query("SELECT * FROM nguoi_dung ORDER BY id DESC LIMIT 50")
                ->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        decryptRow($row, $UP);
    }
}, $iterations, $warmup);

// ── Q4: Địa chỉ giao hàng — 6 trường PII ────────────────────────────────────
echo "Q4 Địa chỉ giao hàng...\n";
$q4sql = runBench('Q4 — SQL thuần', function () use ($pdo, $sampleIds) {
    static $i = 0;
    $st = $pdo->prepare("SELECT * FROM dia_chi WHERE nguoi_dung_id = ?");
    $st->execute([$sampleIds[$i++ % 30]]);
    $st->fetchAll(PDO::FETCH_ASSOC);
}, $iterations, $warmup);

$q4app = runBench('Q4 — Có mã hóa', function () use ($pdo, $sampleIds, $AP) {
    static $i = 0;
    $st = $pdo->prepare("SELECT * FROM dia_chi WHERE nguoi_dung_id = ?");
    $st->execute([$sampleIds[$i++ % 30]]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        decryptRow($row, $AP);
    }
}, $iterations, $warmup);

// ── Q5: Đơn hàng kèm khách hàng (Admin JOIN) — 20 hàng, JOIN + 2 trường PII ──
echo "Q5 Đơn hàng + JOIN khách hàng...\n";
$q5sql = runBench('Q5 — SQL thuần', function () use ($pdo) {
    $pdo->query("
        SELECT d.id, d.ma_don_hang, d.trang_thai, d.tong_tien,
               nd.email, nd.ho_ten, nd.so_dien_thoai
        FROM   don_hang d
        JOIN   nguoi_dung nd ON nd.id = d.nguoi_dung_id
        WHERE  d.ma_don_hang LIKE 'BENCH-%'
        ORDER  BY d.id DESC
        LIMIT  20
    ")->fetchAll(PDO::FETCH_ASSOC);
}, $iterations, $warmup);

$q5app = runBench('Q5 — Có mã hóa', function () use ($pdo) {
    $rows = $pdo->query("
        SELECT d.id, d.ma_don_hang, d.trang_thai, d.tong_tien,
               nd.email, nd.ho_ten, nd.so_dien_thoai
        FROM   don_hang d
        JOIN   nguoi_dung nd ON nd.id = d.nguoi_dung_id
        WHERE  d.ma_don_hang LIKE 'BENCH-%'
        ORDER  BY d.id DESC
        LIMIT  20
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        decryptRow($row, ['ho_ten', 'so_dien_thoai']);
    }
}, $iterations, $warmup);

// ── Q6: Đăng ký tài khoản — INSERT + mã hóa 3 trường ────────────────────────
echo "Q6 Đăng ký (INSERT)...\n";
$q6ctr = (int) ($pdo->query("SELECT MAX(id) FROM nguoi_dung")->fetchColumn()) + 1_000_000;

$q6sql = runBench('Q6 — SQL thuần', function () use ($pdo, &$q6ctr) {
    $e = "tmp" . ($q6ctr++) . "@drop.local";
    $pdo->prepare("
        INSERT INTO nguoi_dung (email, mat_khau_bam, ho_ten, so_dien_thoai, ngay_sinh, vai_tro, trang_thai)
        VALUES (?, 'x', 'Nguyen Van A', '0909000001', '2000-01-01', 'NGUOI_DUNG', 'HOAT_DONG')
    ")->execute([$e]);
    $pdo->prepare("DELETE FROM nguoi_dung WHERE email = ?")->execute([$e]);
}, $iterations, $warmup);

$q6app = runBench('Q6 — Có mã hóa', function () use ($pdo, &$q6ctr) {
    $e = "tmp" . ($q6ctr++) . "@drop.local";
    $pdo->prepare("
        INSERT INTO nguoi_dung (email, mat_khau_bam, ho_ten, so_dien_thoai, ngay_sinh, vai_tro, trang_thai)
        VALUES (?, 'x', ?, ?, ?, 'NGUOI_DUNG', 'HOAT_DONG')
    ")->execute([
        $e,
        EncryptionService::encrypt('Nguyen Van A'),
        EncryptionService::encrypt('0909000001'),
        EncryptionService::encrypt('2000-01-01'),
    ]);
    $pdo->prepare("DELETE FROM nguoi_dung WHERE email = ?")->execute([$e]);
}, $iterations, $warmup);

// ── Q7: Cập nhật thông tin — UPDATE + mã hóa 2 trường ───────────────────────
echo "Q7 Cập nhật thông tin (UPDATE)...\n";
$updateId = $sampleIds[0];

$q7sql = runBench('Q7 — SQL thuần', function () use ($pdo, $updateId) {
    $pdo->prepare("UPDATE nguoi_dung SET ho_ten = ?, so_dien_thoai = ? WHERE id = ?")
        ->execute(['Nguyen Van B', '0909111222', $updateId]);
}, $iterations, $warmup);

$q7app = runBench('Q7 — Có mã hóa', function () use ($pdo, $updateId) {
    $pdo->prepare("UPDATE nguoi_dung SET ho_ten = ?, so_dien_thoai = ? WHERE id = ?")
        ->execute([
            EncryptionService::encrypt('Nguyen Van B'),
            EncryptionService::encrypt('0909111222'),
            $updateId,
        ]);
}, $iterations, $warmup);

// ── Aggregate results ────────────────────────────────────────────────────────
$pairs = [
    ['id' => 'Q1', 'label' => 'Q1 — Đăng nhập',          'detail' => 'WHERE email = ? · 1 hàng · 3 trường PII',  'sql' => $q1sql, 'app' => $q1app],
    ['id' => 'Q2', 'label' => 'Q2 — Trang cá nhân',      'detail' => 'WHERE id = ? · 1 hàng · 3 trường PII',     'sql' => $q2sql, 'app' => $q2app],
    ['id' => 'Q3', 'label' => 'Q3 — DS khách hàng/50',   'detail' => 'ORDER BY id DESC LIMIT 50 · 150 giải mã',  'sql' => $q3sql, 'app' => $q3app],
    ['id' => 'Q4', 'label' => 'Q4 — Địa chỉ giao hàng',  'detail' => 'WHERE nguoi_dung_id = ? · 6 trường PII',   'sql' => $q4sql, 'app' => $q4app],
    ['id' => 'Q5', 'label' => 'Q5 — Đơn hàng + JOIN',    'detail' => 'JOIN nguoi_dung · 20 hàng · 40 giải mã',   'sql' => $q5sql, 'app' => $q5app],
    ['id' => 'Q6', 'label' => 'Q6 — Đăng ký (INSERT)',   'detail' => 'INSERT · 3 trường mã hóa',                 'sql' => $q6sql, 'app' => $q6app],
    ['id' => 'Q7', 'label' => 'Q7 — Cập nhật (UPDATE)',  'detail' => 'UPDATE · 2 trường mã hóa',                 'sql' => $q7sql, 'app' => $q7app],
];

foreach ($pairs as &$p) {
    $overhead        = $p['app']['mean'] - $p['sql']['mean'];
    $p['overhead_ms']  = round($overhead, 4);
    $p['overhead_pct'] = $p['sql']['mean'] > 0
        ? round($overhead / $p['sql']['mean'] * 100, 1)
        : 0.0;
}
unset($p);

// ── Print summary to stdout ──────────────────────────────────────────────────
echo "\n";
printf("%-30s %9s %9s %9s %9s\n", 'Query', 'SQL(ms)', 'App(ms)', 'Diff(ms)', 'Overhead%');
echo str_repeat('─', 70) . "\n";
foreach ($pairs as $p) {
    printf("%-30s %9.4f %9.4f %9.4f %8.1f%%\n",
        $p['id'],
        $p['sql']['mean'],
        $p['app']['mean'],
        $p['overhead_ms'],
        $p['overhead_pct']);
}

// ── Build HTML report ────────────────────────────────────────────────────────
$encEnabled  = KeyManager::isEnabled();
$encLabel    = $encEnabled ? 'BẬT — AES-256-GCM + RSA-2048' : 'TẮT';
$runDate     = date('d/m/Y H:i:s');
$dbUserCount = (int) $pdo->query("SELECT COUNT(*) FROM nguoi_dung WHERE email LIKE 'bm%@bench.local'")->fetchColumn();
$dbAddrCount = $dbUserCount; // 1:1
$dbOrdCount  = (int) $pdo->query("SELECT COUNT(*) FROM don_hang WHERE ma_don_hang LIKE 'BENCH-%'")->fetchColumn();

// Chart data arrays
$labels      = array_column($pairs, 'label');
$sqlMeans    = array_column($pairs, 'sql');
$sqlMeans    = array_map(fn($r) => $r['mean'], $sqlMeans);
$appMeans    = array_column($pairs, 'app');
$appMeans    = array_map(fn($r) => $r['mean'], $appMeans);
$overheads   = array_column($pairs, 'overhead_pct');

$chartLabels   = json_encode($labels,    JSON_UNESCAPED_UNICODE);
$chartSql      = json_encode($sqlMeans);
$chartApp      = json_encode($appMeans);
$chartOverhead = json_encode($overheads);

// Vietnamese conclusion
$avgOverhead  = round(array_sum($overheads) / count($overheads), 1);
$maxOverhead  = max($overheads);
$maxQuery     = $pairs[array_search($maxOverhead, array_column($pairs, 'overhead_pct'))]['label'];
$minOverhead  = min($overheads);
$minQuery     = $pairs[array_search($minOverhead, array_column($pairs, 'overhead_pct'))]['label'];
$maxAppTime   = max($appMeans);
$allFast      = $maxAppTime < 5.0;
$piiPerDecrypt = count($pairs) > 0
    ? round(($pairs[1]['overhead_ms'] / 3), 4) // Q2: 3 fields
    : 0;

$conclusion = "
<p>Qua kết quả benchmark thực hiện trên <strong>{$dbUserCount} bản ghi</strong> người dùng với
<strong>{$iterations} lần lặp</strong> cho mỗi loại truy vấn, có thể rút ra các nhận xét sau:</p>

<ol class='mt-3'>
  <li class='mb-2'>
    <strong>Overhead trung bình toàn hệ thống đạt {$avgOverhead}%</strong> khi bổ sung lớp mã hóa
    AES-256-GCM so với truy vấn SQL thuần túy. Đây là mức overhead chấp nhận được đối với
    ứng dụng thương mại điện tử thực tế.
  </li>
  <li class='mb-2'>
    <strong>Truy vấn bị ảnh hưởng nhiều nhất</strong> là <em>{$maxQuery}</em> với overhead
    <strong>{$maxOverhead}%</strong> — do phải thực hiện nhiều lần giải mã đồng thời trên
    nhiều hàng và nhiều trường PII.
  </li>
  <li class='mb-2'>
    <strong>Truy vấn ít bị ảnh hưởng nhất</strong> là <em>{$minQuery}</em> với chỉ
    <strong>{$minOverhead}%</strong> overhead — phản ánh các truy vấn trả về ít trường PII
    hoặc không cần giải mã phía ứng dụng.
  </li>
  <li class='mb-2'>
    <strong>Chi phí mã hóa/giải mã một trường PII</strong> ước tính khoảng
    <strong>{$piiPerDecrypt} ms</strong> (đo qua Q2: 1 hàng × 3 trường),
    phản ánh độ trễ của phép toán AES-256-GCM kết hợp base64 và xác thực GCM tag.
  </li>
  <li class='mb-2'>
    <strong>Truy vấn ghi dữ liệu (Q6 — Đăng ký, Q7 — Cập nhật)</strong> có overhead thấp hơn
    các truy vấn đọc nhiều hàng, bởi chỉ mã hóa 2–3 trường PII trước khi INSERT/UPDATE.
  </li>
  <li class='mb-2'>
    " . ($allFast
        ? "Toàn bộ {" . count($pairs) . "} loại truy vấn đều hoàn thành trong dưới <strong>5 ms</strong>,
           đảm bảo trải nghiệm người dùng mượt mà ngay cả với lớp mã hóa được bật."
        : "Một số truy vấn vượt ngưỡng 5 ms; cần cân nhắc caching tầng ứng dụng cho các
           trang danh sách quản trị để giảm tải giải mã lặp lại.")
    . "
  </li>
</ol>

<p class='mt-3 mb-0'>
  <strong>Kết luận chung:</strong> Việc áp dụng mã hóa <strong>AES-256-GCM</strong> kết hợp
  quản lý khóa <strong>RSA-2048 OAEP</strong> để bảo vệ dữ liệu PII nhạy cảm (họ tên, số điện
  thoại, ngày sinh, địa chỉ) mang lại sự đánh đổi hợp lý giữa bảo mật và hiệu năng. Với mức
  overhead trung bình <strong>{$avgOverhead}%</strong> và thời gian phản hồi vẫn ở mức chấp nhận
  được, mô hình mã hóa theo tầng (Tier A/B/C) đã triển khai là giải pháp phù hợp cho hệ thống
  thương mại điện tử quy mô vừa và nhỏ tại Việt Nam.
</p>
";

// ── HTML ─────────────────────────────────────────────────────────────────────
$statsRows = '';
foreach ($pairs as $p) {
    $bg = $p['overhead_pct'] > 100 ? '#fff5f5' : ($p['overhead_pct'] > 40 ? '#fffbeb' : '#f0fdf4');
    $badge = $p['overhead_pct'] > 100
        ? "<span class='badge' style='background:#ef4444'>{$p['overhead_pct']}%</span>"
        : ($p['overhead_pct'] > 40
            ? "<span class='badge' style='background:#f59e0b'>{$p['overhead_pct']}%</span>"
            : "<span class='badge' style='background:#22c55e'>{$p['overhead_pct']}%</span>");
    $statsRows .= "
        <tr>
            <td rowspan='2' class='align-middle fw-semibold' style='background:{$bg}'>{$p['id']}</td>
            <td rowspan='2' class='align-middle small text-secondary' style='background:{$bg}'>{$p['detail']}</td>
            <td><span class='badge bg-primary'>SQL thuần</span></td>
            <td>{$p['sql']['min']}</td>
            <td><strong>{$p['sql']['mean']}</strong></td>
            <td>{$p['sql']['median']}</td>
            <td>{$p['sql']['p95']}</td>
            <td>{$p['sql']['max']}</td>
            <td rowspan='2' class='align-middle text-center' style='background:{$bg}'>{$badge}<br>
                <small class='text-muted'>+{$p['overhead_ms']} ms</small></td>
        </tr>
        <tr style='background:{$bg}'>
            <td><span class='badge bg-danger'>Có mã hóa</span></td>
            <td>{$p['app']['min']}</td>
            <td><strong>{$p['app']['mean']}</strong></td>
            <td>{$p['app']['median']}</td>
            <td>{$p['app']['p95']}</td>
            <td>{$p['app']['max']}</td>
        </tr>
    ";
}

$html = <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Báo cáo Hiệu năng — Mã hóa AES-256-GCM</title>
<link  rel="stylesheet"
       href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<style>
  body        { background: #f1f5f9; }
  .hero       { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
                color: #fff; padding: 3rem 0 2.5rem; }
  .hero small { opacity: .75; }
  .stat-card  { border: none; border-radius: .75rem; box-shadow: 0 1px 6px rgba(0,0,0,.08); }
  .section    { border: none; border-radius: .75rem; box-shadow: 0 1px 6px rgba(0,0,0,.08);
                margin-bottom: 2rem; }
  .conclusion { border-left: 5px solid #2563eb; background: #fff; border-radius: .75rem;
                box-shadow: 0 1px 6px rgba(0,0,0,.08); }
  th, td      { vertical-align: middle !important; }
</style>
</head>
<body>

<!-- ── Hero ─────────────────────────────────────────────────────────────── -->
<div class="hero text-center">
  <div class="container">
    <h1 class="fw-bold display-5 mb-2">Báo cáo Hiệu năng Truy vấn CSDL</h1>
    <p class="lead mb-1">Hệ thống Thương mại điện tử — Phân tích overhead khi áp dụng mã hóa AES-256-GCM + RSA-2048</p>
    <small>Thực hiện: {$runDate} &nbsp;|&nbsp; Mã hóa: {$encLabel}</small>
  </div>
</div>

<div class="container py-5">

<!-- ── Summary cards ────────────────────────────────────────────────────── -->
<div class="row g-3 mb-5">
  <div class="col-6 col-md-3">
    <div class="card stat-card text-center p-3">
      <div class="display-6 fw-bold text-primary">{$dbUserCount}</div>
      <div class="text-secondary small">Người dùng test</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card text-center p-3">
      <div class="display-6 fw-bold text-success">{$dbAddrCount}</div>
      <div class="text-secondary small">Địa chỉ test</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card text-center p-3">
      <div class="display-6 fw-bold text-warning">{$dbOrdCount}</div>
      <div class="text-secondary small">Đơn hàng test</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card text-center p-3">
      <div class="display-6 fw-bold text-danger">{$iterations}</div>
      <div class="text-secondary small">Lần lặp / query</div>
    </div>
  </div>
</div>

<!-- ── Chart 1: Mean query time ─────────────────────────────────────────── -->
<div class="card section">
  <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
    <h5 class="fw-bold mb-0">📊 Thời gian Truy vấn Trung bình (ms)</h5>
    <p class="text-secondary small mb-0">So sánh thời gian SQL thuần với thời gian đầy đủ khi có mã hóa AES-256-GCM</p>
  </div>
  <div class="card-body p-4">
    <canvas id="chartMean" height="90"></canvas>
  </div>
</div>

<!-- ── Chart 2: Overhead % ──────────────────────────────────────────────── -->
<div class="card section">
  <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
    <h5 class="fw-bold mb-0">⚡ Overhead do Mã hóa (%)</h5>
    <p class="text-secondary small mb-0">
      Phần trăm tăng thêm của mỗi truy vấn khi bổ sung lớp mã hóa/giải mã AES-256-GCM tại tầng ứng dụng.
      <span class="text-success fw-semibold">Xanh &lt; 40%</span> ·
      <span class="text-warning fw-semibold">Vàng 40–100%</span> ·
      <span class="text-danger fw-semibold">Đỏ &gt; 100%</span>
    </p>
  </div>
  <div class="card-body p-4">
    <canvas id="chartOverhead" height="70"></canvas>
  </div>
</div>

<!-- ── Stats table ──────────────────────────────────────────────────────── -->
<div class="card section">
  <div class="card-header bg-white border-0 pt-4 pb-2 px-4">
    <h5 class="fw-bold mb-0">📋 Thống kê Chi tiết (ms)</h5>
    <p class="text-secondary small mb-0">Tất cả giá trị đơn vị milli-giây (ms) — {$iterations} lần lặp, warmup {$warmup} lần</p>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-sm mb-0" style="font-size:.85rem">
        <thead class="table-dark">
          <tr>
            <th>Query</th><th>Mô tả</th><th>Chế độ</th>
            <th>Min</th><th>Trung bình</th><th>Trung vị</th>
            <th>P95</th><th>Max</th><th>Overhead</th>
          </tr>
        </thead>
        <tbody>{$statsRows}</tbody>
      </table>
    </div>
  </div>
</div>

<!-- ── Conclusion ───────────────────────────────────────────────────────── -->
<div class="card conclusion p-4 mb-4">
  <h4 class="fw-bold text-primary mb-3">🔍 Kết luận</h4>
  {$conclusion}
</div>

<p class="text-center text-muted small pb-4">
  Báo cáo được sinh tự động bởi
  <code>scripts/run_query_benchmark.php</code> &nbsp;·&nbsp;
  Dự án: <em>ecom_clothes_web — Đồ án môn Nhập môn An toàn Thông tin</em>
</p>

</div><!-- /container -->

<script>
const LABELS   = {$chartLabels};
const SQL_DATA = {$chartSql};
const APP_DATA = {$chartApp};
const OH_DATA  = {$chartOverhead};

const overheadColors = OH_DATA.map(v =>
    v > 100 ? 'rgba(239,68,68,.85)'
            : v > 40  ? 'rgba(245,158,11,.85)'
                      : 'rgba(34,197,94,.85)'
);

// Chart 1 — grouped bar
new Chart(document.getElementById('chartMean'), {
  type: 'bar',
  data: {
    labels: LABELS,
    datasets: [
      {
        label: 'SQL thuần (không giải mã)',
        data: SQL_DATA,
        backgroundColor: 'rgba(37,99,235,.75)',
        borderColor:     'rgba(37,99,235,1)',
        borderWidth: 1,
        borderRadius: 4,
      },
      {
        label: 'Có mã hóa AES-256-GCM',
        data: APP_DATA,
        backgroundColor: 'rgba(239,68,68,.75)',
        borderColor:     'rgba(239,68,68,1)',
        borderWidth: 1,
        borderRadius: 4,
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      tooltip: {
        callbacks: {
          label: ctx => ' ' + ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(4) + ' ms',
        },
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        title: { display: true, text: 'Thời gian (ms)' },
        ticks: { callback: v => v.toFixed(2) + ' ms' },
      },
      x: { ticks: { font: { size: 11 } } },
    },
  },
});

// Chart 2 — overhead %
new Chart(document.getElementById('chartOverhead'), {
  type: 'bar',
  data: {
    labels: LABELS,
    datasets: [{
      label: 'Overhead (%)',
      data: OH_DATA,
      backgroundColor: overheadColors,
      borderColor:     overheadColors.map(c => c.replace('.85', '1')),
      borderWidth: 1,
      borderRadius: 4,
    }],
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ' Overhead: +' + ctx.parsed.x.toFixed(1) + '%',
        },
      },
    },
    scales: {
      x: {
        beginAtZero: true,
        title: { display: true, text: 'Overhead (%)' },
        ticks: { callback: v => v + '%' },
      },
    },
  },
});
</script>
</body>
</html>
HTML;

$outFile = $root . '/public/benchmark_report.html';
file_put_contents($outFile, $html);

echo "\n✓ Báo cáo đã lưu tại: public/benchmark_report.html\n";
echo "  Mở tại: http://localhost/ecom_clothes_web/public/benchmark_report.html\n";
