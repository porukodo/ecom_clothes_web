<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/Env.php';

/**
 * Unwraps the AES data key using RSA private key from environment variables.
 * The raw AES key is never stored in source code or the database.
 */
final class KeyManager
{
    private static ?string $aesKey = null;

    public static function isEnabled(): bool
    {
        Env::load();
        return Env::isTrue('ENCRYPTION_ENABLED', false);
    }

    public static function aesKey(): string
    {
        if (self::$aesKey !== null) {
            return self::$aesKey;
        }

        if (!self::isEnabled()) {
            throw new RuntimeException('Encryption is disabled (ENCRYPTION_ENABLED=false)');
        }

        $privatePem = Env::require('RSA_PRIVATE_KEY');
        $encryptedB64 = Env::require('ENCRYPTED_AES_KEY');

        $privateKey = openssl_pkey_get_private($privatePem);
        if ($privateKey === false) {
            throw new RuntimeException('Invalid RSA_PRIVATE_KEY in environment');
        }

        $encrypted = base64_decode($encryptedB64, true);
        if ($encrypted === false) {
            throw new RuntimeException('Invalid ENCRYPTED_AES_KEY (not valid base64)');
        }

        $aesKey = '';
        $ok = openssl_private_decrypt(
            $encrypted,
            $aesKey,
            $privateKey,
            OPENSSL_PKCS1_OAEP_PADDING
        );

        if (!$ok || strlen($aesKey) !== 32) {
            throw new RuntimeException('Failed to unwrap AES key with RSA private key');
        }

        self::$aesKey = $aesKey;
        return self::$aesKey;
    }
}
