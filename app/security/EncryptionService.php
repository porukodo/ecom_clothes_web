<?php
declare(strict_types=1);

require_once __DIR__ . '/KeyManager.php';

/**
 * AES-256-GCM field-level encryption for customer PII (Tier A & B).
 */
final class EncryptionService
{
    private const VERSION_PREFIX = 'ENC:v1:';
    private const IV_LENGTH = 12;
    private const TAG_LENGTH = 16;

    public static function isEncrypted(?string $value): bool
    {
        return $value !== null && $value !== '' && str_starts_with($value, self::VERSION_PREFIX);
    }

    public static function encrypt(?string $plaintext): ?string
    {
        if ($plaintext === null || $plaintext === '') {
            return $plaintext;
        }

        if (!KeyManager::isEnabled()) {
            return $plaintext;
        }

        $key = KeyManager::aesKey();
        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';

        $ciphertext = openssl_encrypt(
            $plaintext,
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH
        );

        if ($ciphertext === false) {
            throw new RuntimeException('AES encryption failed');
        }

        return self::VERSION_PREFIX . base64_encode($iv . $ciphertext . $tag);
    }

    public static function decrypt(?string $stored): ?string
    {
        if ($stored === null || $stored === '') {
            return $stored;
        }

        if (!self::isEncrypted($stored)) {
            return $stored;
        }

        if (!KeyManager::isEnabled()) {
            throw new RuntimeException('Cannot decrypt PII: ENCRYPTION_ENABLED is false');
        }

        $raw = base64_decode(substr($stored, strlen(self::VERSION_PREFIX)), true);
        if ($raw === false || strlen($raw) < self::IV_LENGTH + self::TAG_LENGTH + 1) {
            throw new RuntimeException('Invalid encrypted payload');
        }

        $iv = substr($raw, 0, self::IV_LENGTH);
        $tag = substr($raw, -self::TAG_LENGTH);
        $ciphertext = substr($raw, self::IV_LENGTH, -self::TAG_LENGTH);

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            KeyManager::aesKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plaintext === false) {
            throw new RuntimeException('AES decryption failed (wrong key or corrupted data)');
        }

        return $plaintext;
    }

    /** @param list<string> $fields */
    public static function encryptFields(array $row, array $fields): array
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $row) && $row[$field] !== null && $row[$field] !== '') {
                $row[$field] = self::encrypt((string)$row[$field]);
            }
        }
        return $row;
    }

    /** @param list<string> $fields */
    public static function decryptFields(array $row, array $fields): array
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $row) && $row[$field] !== null && $row[$field] !== '') {
                $row[$field] = self::decrypt((string)$row[$field]);
            }
        }
        return $row;
    }
}
