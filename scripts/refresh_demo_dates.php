<?php
/**
 * Re-spread benchmark users' / orders' timestamps across the last ~6 months
 * ending "now", so the admin dashboard's default "this month"/"this year"
 * filters, the revenue chart, the order-ratio bars, and the best-sellers list
 * always have something to show — regardless of how long ago `database.sql`
 * was generated.
 *
 * The seeded `database.sql` freezes `tao_luc` at the moment the seed script
 * ran (a single instant for all 2 000 orders / 4 000 users). As real time
 * moves on, that instant drops out of "this month" and the dashboard looks
 * empty. This script only rewrites timestamps on rows it can identify as
 * benchmark data (`bm%@bench.local` users, `BENCH-%` orders) — no PII is
 * touched.
 *
 * Usage:
 *   php scripts/refresh_demo_dates.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Forbidden: CLI only.\n");
}

$root = dirname(__DIR__);
require_once $root . '/app/Database.php';

$pdo  = Database::pdo();
$days = 180;

function randomRecentDateTime(int $maxDaysAgo): string
{
    $secondsAgo = random_int(0, $maxDaysAgo * 86400);
    return date('Y-m-d H:i:s', time() - $secondsAgo);
}

function randomDateTimeAfter(string $from): string
{
    $base          = strtotime($from);
    $secondsLater  = random_int(0, 4 * 86400);
    return date('Y-m-d H:i:s', min(time(), $base + $secondsLater));
}

echo "Spreading benchmark timestamps across the last {$days} days (relative to now)...\n";

// ── Benchmark users ──────────────────────────────────────────────────────────
$userIds = $pdo->query("SELECT id FROM nguoi_dung WHERE email LIKE 'bm%@bench.local'")
               ->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("UPDATE nguoi_dung SET tao_luc = ?, cap_nhat_luc = ? WHERE id = ?");
$pdo->beginTransaction();
foreach ($userIds as $i => $id) {
    $created = randomRecentDateTime($days);
    $stmt->execute([$created, randomDateTimeAfter($created), $id]);
    if (($i + 1) % 500 === 0) {
        $pdo->commit();
        $pdo->beginTransaction();
    }
}
$pdo->commit();
echo "  done " . count($userIds) . " benchmark users\n";

// ── Benchmark orders ─────────────────────────────────────────────────────────
$orderIds = $pdo->query("SELECT id FROM don_hang WHERE ma_don_hang LIKE 'BENCH-%'")
                ->fetchAll(PDO::FETCH_COLUMN);

$orderStmt = $pdo->prepare("UPDATE don_hang SET tao_luc = ?, cap_nhat_luc = ? WHERE id = ?");
$lineStmt  = $pdo->prepare("UPDATE chi_tiet_don_hang SET tao_luc = ? WHERE don_hang_id = ?");
$pdo->beginTransaction();
foreach ($orderIds as $i => $id) {
    $created = randomRecentDateTime($days);
    $orderStmt->execute([$created, randomDateTimeAfter($created), $id]);
    $lineStmt->execute([$created, $id]);
    if (($i + 1) % 500 === 0) {
        $pdo->commit();
        $pdo->beginTransaction();
    }
}
$pdo->commit();
echo "  done " . count($orderIds) . " benchmark orders (+ their line items)\n";

echo "\nDone. Reload the admin dashboard — \"this month\" / \"this year\" should now show data.\n";
