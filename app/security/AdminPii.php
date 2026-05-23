<?php
declare(strict_types=1);

require_once __DIR__ . '/EncryptionService.php';
require_once __DIR__ . '/PiiFields.php';

/** Decrypt PII columns on admin-fetched rows (JOINs, raw PDO). */
final class AdminPii
{
    public static function decryptUserRow(array $row): array
    {
        return EncryptionService::decryptFields($row, PiiFields::USER);
    }

    /** @param list<array<string, mixed>> $rows */
    public static function decryptUserRows(array $rows): array
    {
        return array_map([self::class, 'decryptUserRow'], $rows);
    }

    public static function decryptOrderRow(array $row): array
    {
        return EncryptionService::decryptFields($row, PiiFields::ORDER);
    }

    /** @param list<array<string, mixed>> $rows */
    public static function decryptOrderRows(array $rows): array
    {
        return array_map([self::class, 'decryptOrderRow'], $rows);
    }
}
