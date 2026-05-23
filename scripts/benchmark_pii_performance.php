<?php
/**
 * PII query performance benchmark — compare plain (main) vs encrypted (V2).
 *
 * Usage:
 *   php scripts/benchmark_pii_performance.php --mode=plain --label=main
 *   php scripts/benchmark_pii_performance.php --mode=encrypted --label=V2
 *
 * Output: JSON to stdout (also written to scripts/benchmark_results_<label>.json)
 */
declare(strict_types=1);

$phpBin = PHP_BINARY;
$projectRoot = dirname(__DIR__);

// --- CLI args ---
$mode = 'plain';
$label = 'unknown';
$iterations = 500;
$warmup = 50;

foreach ($argv as $arg) {
    if (str_starts_with($arg, '--mode=')) {
        $mode = substr($arg, 7);
    } elseif (str_starts_with($arg, '--label=')) {
        $label = substr($arg, 8);
    } elseif (str_starts_with($arg, '--iterations=')) {
        $iterations = max(10, (int)substr($arg, 13));
    }
}

if (!in_array($mode, ['plain', 'encrypted'], true)) {
    fwrite(STDERR, "mode must be plain or encrypted\n");
    exit(1);
}

require_once $projectRoot . '/app/Database.php';

$useEncryption = ($mode === 'encrypted');
if ($useEncryption) {
    require_once $projectRoot . '/app/bootstrap.php';
    require_once $projectRoot . '/app/security/KeyManager.php';
    require_once $projectRoot . '/app/security/EncryptionService.php';
    require_once $projectRoot . '/app/security/PiiFields.php';

    if (!KeyManager::isEnabled()) {
        fwrite(STDERR, "ENCRYPTION_ENABLED must be true in .env for encrypted mode\n");
        exit(1);
    }
    KeyManager::aesKey();
}

// Sample PII resembling production data
$sampleUser = [
    'ho_ten' => 'Bùi Lê Minh Phát',
    'so_dien_thoai' => '0909119189',
    'ngay_sinh' => '2005-10-12',
];
$sampleAddress = [
    'ten_nguoi_nhan' => 'Bùi Lê Minh Phát',
    'so_dien_thoai' => '0909119189',
    'tinh_thanh' => 'Thành phố Hà Nội',
    'quan_huyen' => 'Quận Hoàn Kiếm',
    'phuong_xa' => 'Phường Hàng Mã',
    'dia_chi_cu_the' => '241 Trần Hưng Đạo',
];

function bench(string $name, callable $fn, int $iterations, int $warmup): array
{
    for ($i = 0; $i < $warmup; $i++) {
        $fn();
    }

    $times = [];
    for ($i = 0; $i < $iterations; $i++) {
        $start = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $start) / 1e6;
    }

    sort($times);
    $n = count($times);
    $sum = array_sum($times);

    return [
        'name' => $name,
        'iterations' => $iterations,
        'mean_ms' => round($sum / $n, 4),
        'median_ms' => round($times[(int)floor($n / 2)], 4),
        'p95_ms' => round($times[(int)floor($n * 0.95)], 4),
        'min_ms' => round($times[0], 4),
        'max_ms' => round($times[$n - 1], 4),
        'total_ms' => round($sum, 2),
    ];
}

function transformField(string $value, bool $encrypt): string
{
    if (!$encrypt) {
        return $value;
    }
    return EncryptionService::encrypt($value) ?? $value;
}

function reverseField(string $value, bool $encrypt): string
{
    if (!$encrypt) {
        return $value;
    }
    return EncryptionService::decrypt($value) ?? $value;
}

// --- MySQL availability ---
$dbAvailable = false;
$dbError = null;
$pdo = null;
$testUserId = 3;
$testEmail = 'pbui02@gmail.com';

try {
    $pdo = Database::pdo();
    $pdo->query('SELECT 1');
    $dbAvailable = true;
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

// --- SQLite fallback for raw SQL shape comparison ---
$sqlitePdo = new PDO('sqlite::memory:');
$sqlitePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sqlitePdo->exec('
    CREATE TABLE nguoi_dung (
        id INTEGER PRIMARY KEY,
        email TEXT NOT NULL,
        ho_ten TEXT,
        so_dien_thoai TEXT,
        ngay_sinh TEXT
    );
    CREATE TABLE dia_chi (
        id INTEGER PRIMARY KEY,
        nguoi_dung_id INTEGER,
        ten_nguoi_nhan TEXT,
        so_dien_thoai TEXT,
        tinh_thanh TEXT,
        quan_huyen TEXT,
        phuong_xa TEXT,
        dia_chi_cu_the TEXT
    );
');
for ($i = 1; $i <= 200; $i++) {
    $sqlitePdo->prepare('INSERT INTO nguoi_dung (id, email, ho_ten, so_dien_thoai, ngay_sinh) VALUES (?,?,?,?,?)')
        ->execute([$i, "user{$i}@test.com", "User {$i}", "090000000{$i}", '2000-01-01']);
    $sqlitePdo->prepare('INSERT INTO dia_chi (nguoi_dung_id, ten_nguoi_nhan, so_dien_thoai, tinh_thanh, quan_huyen, phuong_xa, dia_chi_cu_the) VALUES (?,?,?,?,?,?,?)')
        ->execute([$i, "User {$i}", "090000000{$i}", 'Hanoi', 'Hoan Kiem', 'Hang Ma', "Street {$i}"]);
}

$benchmarks = [];

// 1) Crypto: single field encrypt
$benchmarks[] = bench(
    'crypto_encrypt_single_field',
    fn() => transformField($sampleUser['ho_ten'], $useEncryption),
    $iterations,
    $warmup
);

// 2) Crypto: single field decrypt (pre-encrypt once)
$storedName = transformField($sampleUser['ho_ten'], $useEncryption);
$benchmarks[] = bench(
    'crypto_decrypt_single_field',
    fn() => reverseField($storedName, $useEncryption),
    $iterations,
    $warmup
);

// 3) Crypto: full user row (3 fields)
$benchmarks[] = bench(
    'crypto_encrypt_user_row_3_fields',
    function () use ($sampleUser, $useEncryption) {
        foreach ($sampleUser as $v) {
            transformField($v, $useEncryption);
        }
    },
    $iterations,
    $warmup
);

// 4) Crypto: full address row (6 fields)
$benchmarks[] = bench(
    'crypto_encrypt_address_row_6_fields',
    function () use ($sampleAddress, $useEncryption) {
        foreach ($sampleAddress as $v) {
            transformField($v, $useEncryption);
        }
    },
    $iterations,
    $warmup
);

// 5) Simulated read path: decrypt user row after fetch
$encryptedUser = [];
foreach ($sampleUser as $k => $v) {
    $encryptedUser[$k] = transformField($v, $useEncryption);
}
$benchmarks[] = bench(
    'app_decrypt_user_row_after_select',
    function () use ($encryptedUser, $useEncryption) {
        foreach ($encryptedUser as $v) {
            reverseField($v, $useEncryption);
        }
    },
    $iterations,
    $warmup
);

// SQLite SQL benchmarks (same SQL on both branches)
$benchmarks[] = bench(
    'sql_select_user_by_email',
    function () use ($sqlitePdo) {
        $stm = $sqlitePdo->prepare('SELECT * FROM nguoi_dung WHERE email = ? LIMIT 1');
        $stm->execute(['user50@test.com']);
        $stm->fetch();
    },
    $iterations,
    $warmup
);

$benchmarks[] = bench(
    'sql_select_user_by_id',
    function () use ($sqlitePdo) {
        $stm = $sqlitePdo->prepare('SELECT id, email, ho_ten, ngay_sinh, so_dien_thoai FROM nguoi_dung WHERE id = ? LIMIT 1');
        $stm->execute([50]);
        $stm->fetch();
    },
    $iterations,
    $warmup
);

$benchmarks[] = bench(
    'sql_select_addresses_by_user',
    function () use ($sqlitePdo) {
        $stm = $sqlitePdo->prepare('SELECT * FROM dia_chi WHERE nguoi_dung_id = ? ORDER BY id DESC');
        $stm->execute([50]);
        $stm->fetchAll();
    },
    $iterations,
    $warmup
);

// End-to-end simulated: SQL fetch + app decrypt (V2) or noop (main)
$benchmarks[] = bench(
    'e2e_select_user_by_id_plus_app_layer',
    function () use ($sqlitePdo, $encryptedUser, $useEncryption) {
        $stm = $sqlitePdo->prepare('SELECT id, email, ho_ten, so_dien_thoai, ngay_sinh FROM nguoi_dung WHERE id = ?');
        $stm->execute([50]);
        $row = $stm->fetch(PDO::FETCH_ASSOC);
        if ($useEncryption) {
            foreach (['ho_ten', 'so_dien_thoai', 'ngay_sinh'] as $f) {
                if (!empty($row[$f])) {
                    reverseField($row[$f], $useEncryption);
                }
            }
        }
    },
    $iterations,
    $warmup
);

$benchmarks[] = bench(
    'e2e_select_addresses_plus_app_layer',
    function () use ($sqlitePdo, $sampleAddress, $useEncryption) {
        $stm = $sqlitePdo->prepare('SELECT * FROM dia_chi WHERE nguoi_dung_id = ?');
        $stm->execute([50]);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        if ($useEncryption) {
            foreach ($rows as $row) {
                foreach (array_keys($sampleAddress) as $f) {
                    if (!empty($row[$f])) {
                        reverseField($row[$f], $useEncryption);
                    }
                }
            }
        }
    },
    $iterations,
    $warmup
);

// MySQL real DB benchmarks when available
$mysqlBenchmarks = [];
if ($dbAvailable && $pdo) {
    $mysqlBenchmarks[] = bench(
        'mysql_select_user_by_email',
        function () use ($pdo, $testEmail) {
            $stm = $pdo->prepare('SELECT * FROM nguoi_dung WHERE email = ? LIMIT 1');
            $stm->execute([$testEmail]);
            $stm->fetch();
        },
        min($iterations, 200),
        $warmup
    );

    $mysqlBenchmarks[] = bench(
        'mysql_select_user_by_id',
        function () use ($pdo, $testUserId) {
            $stm = $pdo->prepare('SELECT id, email, ho_ten, ngay_sinh, so_dien_thoai FROM nguoi_dung WHERE id = ? LIMIT 1');
            $stm->execute([$testUserId]);
            $stm->fetch();
        },
        min($iterations, 200),
        $warmup
    );

    $mysqlBenchmarks[] = bench(
        'mysql_select_addresses_by_user',
        function () use ($pdo, $testUserId) {
            $stm = $pdo->prepare('SELECT * FROM dia_chi WHERE nguoi_dung_id = ? ORDER BY mac_dinh DESC, id DESC');
            $stm->execute([$testUserId]);
            $stm->fetchAll();
        },
        min($iterations, 200),
        $warmup
    );

    if ($useEncryption) {
        require_once dirname(__DIR__) . '/app/models/User.php';
        require_once dirname(__DIR__) . '/app/models/Address.php';

        $mysqlBenchmarks[] = bench(
            'mysql_model_timTheoEmail_with_decrypt',
            fn() => User::timTheoEmail($testEmail),
            min($iterations, 200),
            $warmup
        );

        $mysqlBenchmarks[] = bench(
            'mysql_model_layTheoUser_with_decrypt',
            fn() => Address::layTheoUser($testUserId),
            min($iterations, 200),
            $warmup
        );
    } else {
        $mysqlBenchmarks[] = bench(
            'mysql_model_timTheoEmail_plain',
            function () use ($pdo, $testEmail) {
                $stm = $pdo->prepare('SELECT * FROM nguoi_dung WHERE email = ? LIMIT 1');
                $stm->execute([$testEmail]);
                $stm->fetch();
            },
            min($iterations, 200),
            $warmup
        );

        $mysqlBenchmarks[] = bench(
            'mysql_model_layTheoUser_plain',
            function () use ($pdo, $testUserId) {
                $stm = $pdo->prepare('SELECT * FROM dia_chi WHERE nguoi_dung_id = ? ORDER BY mac_dinh DESC, id DESC');
                $stm->execute([$testUserId]);
                $stm->fetchAll();
            },
            min($iterations, 200),
            $warmup
        );
    }
}

$result = [
    'label' => $label,
    'mode' => $mode,
    'encryption_enabled' => $useEncryption,
    'iterations' => $iterations,
    'warmup' => $warmup,
    'timestamp' => date('c'),
    'php_version' => PHP_VERSION,
    'mysql_available' => $dbAvailable,
    'mysql_error' => $dbError,
    'benchmarks' => $benchmarks,
    'mysql_benchmarks' => $mysqlBenchmarks,
];

$outFile = $projectRoot . '/scripts/benchmark_results_' . $label . '.json';
file_put_contents($outFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
