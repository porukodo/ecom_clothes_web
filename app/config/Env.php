<?php
declare(strict_types=1);

/**
 * Minimal .env loader (no Composer). Loads into $_ENV only — not putenv().
 * Industry equivalent: vlucas/phpdotenv when Composer is available.
 */
final class Env
{
    private static bool $loaded = false;

    public static function load(?string $path = null): void
    {
        if (self::$loaded) {
            return;
        }

        $path ??= dirname(__DIR__, 2) . '/.env';
        if (!is_readable($path)) {
            self::$loaded = true;
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new RuntimeException('Cannot read .env file');
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            if ($name === '' || !preg_match('/^[A-Z][A-Z0-9_]*$/', $name)) {
                continue;
            }

            $value = trim($value);
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"'))
                || (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
                $value = str_replace(['\\n', '\\r'], ["\n", "\r"], $value);
            }

            $_ENV[$name] = $value;
        }

        self::$loaded = true;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        self::load();
        return $_ENV[$key] ?? $default;
    }

    public static function require(string $key): string
    {
        $value = self::get($key);
        if ($value === null || $value === '') {
            throw new RuntimeException("Missing required environment variable: {$key}");
        }
        return $value;
    }

    public static function isTrue(string $key, bool $default = false): bool
    {
        $value = self::get($key);
        if ($value === null) {
            return $default;
        }
        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }
}
