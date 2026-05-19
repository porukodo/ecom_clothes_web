<?php
/**
 * Encrypt existing plaintext PII (Tier A + B) in the database.
 * Run once on V2 after schema migration and .env keys are configured.
 *
 *   php scripts/migrate_encrypt_existing_data.php
 */
declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require_once $projectRoot . '/app/bootstrap.php';
require_once $projectRoot . '/app/Database.php';
require_once $projectRoot . '/app/security/KeyManager.php';
require_once $projectRoot . '/app/security/EncryptionService.php';
require_once $projectRoot . '/app/security/PiiFields.php';

if (php_sapi_name() !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

if (!KeyManager::isEnabled()) {
    fwrite(STDERR, "Set ENCRYPTION_ENABLED=true in .env before running migration.\n");
    exit(1);
}

// Force AES key load early
KeyManager::aesKey();

$pdo = Database::pdo();
$userFields = PiiFields::USER;
$addressFields = PiiFields::ADDRESS;

echo "Encrypting nguoi_dung PII...\n";
$users = $pdo->query('SELECT id, ho_ten, so_dien_thoai, ngay_sinh FROM nguoi_dung')->fetchAll(PDO::FETCH_ASSOC);
$userUpdates = 0;

foreach ($users as $user) {
    $sets = [];
    $params = ['id' => $user['id']];

    foreach ($userFields as $field) {
        $value = $user[$field];
        if ($value === null || $value === '' || EncryptionService::isEncrypted((string)$value)) {
            continue;
        }
        $sets[] = "{$field} = :{$field}";
        $params[$field] = EncryptionService::encrypt((string)$value);
    }

    if ($sets === []) {
        continue;
    }

    $sql = 'UPDATE nguoi_dung SET ' . implode(', ', $sets) . ' WHERE id = :id';
    $pdo->prepare($sql)->execute($params);
    $userUpdates++;
}

echo "  Updated {$userUpdates} user row(s).\n";

echo "Encrypting dia_chi PII...\n";
$addresses = $pdo->query('SELECT * FROM dia_chi')->fetchAll(PDO::FETCH_ASSOC);
$addressUpdates = 0;

foreach ($addresses as $row) {
    $sets = [];
    $params = ['id' => $row['id']];

    foreach ($addressFields as $field) {
        $value = $row[$field] ?? null;
        if ($value === null || $value === '' || EncryptionService::isEncrypted((string)$value)) {
            continue;
        }
        $sets[] = "{$field} = :{$field}";
        $params[$field] = EncryptionService::encrypt((string)$value);
    }

    if ($sets === []) {
        continue;
    }

    $sql = 'UPDATE dia_chi SET ' . implode(', ', $sets) . ' WHERE id = :id';
    $pdo->prepare($sql)->execute($params);
    $addressUpdates++;
}

echo "  Updated {$addressUpdates} address row(s).\n";
echo "Done.\n";
