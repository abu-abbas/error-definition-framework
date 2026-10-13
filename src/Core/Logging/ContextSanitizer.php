<?php

declare(strict_types=1);

namespace Edf\Core\Logging;

use BackedEnum;
use DateTimeInterface;

/**
 * Membersihkan runtime context dari data sensitif sebelum diteruskan ke logger.
 *
 * Value pada sensitive key diganti dengan [REDACTED] tanpa mengubah key atau
 * struktur context. Key dicocokkan secara utuh setelah lower-case dan semua
 * karakter selain huruf dan angka dihapus, jadi `accessToken` sama dengan
 * `access_token`, tetapi `token_count` tidak sama dengan `token`.
 *
 * @see docs/decisions/1-error-definition-framework/1.7-logging-dan-context.md
 */
final class ContextSanitizer
{
    public const REDACTED = '[REDACTED]';

    /**
     * Baseline package; konfigurasi aplikasi hanya dapat menambah.
     */
    public const DEFAULT_SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'access_token',
        'refresh_token',
        'authorization',
        'cookie',
        'secret',
        'api_key',
        'client_secret',
        'private_key',
    ];

    /** @var array<string, true> */
    private readonly array $sensitiveKeys;

    /**
     * @param list<string> $additionalSensitiveKeys
     */
    public function __construct(array $additionalSensitiveKeys = [])
    {
        $keys = [];

        foreach ([...self::DEFAULT_SENSITIVE_KEYS, ...$additionalSensitiveKeys] as $key) {
            $keys[self::normalizeKey($key)] = true;
        }

        $this->sensitiveKeys = $keys;
    }

    /**
     * @param array<array-key, mixed> $context
     * @return array<array-key, mixed>
     */
    public function sanitize(array $context): array
    {
        $sanitized = [];

        foreach ($context as $key => $value) {
            $sanitized[$key] = isset($this->sensitiveKeys[self::normalizeKey((string) $key)])
                ? self::REDACTED
                : $this->normalizeValue($value);
        }

        return $sanitized;
    }

    private function normalizeValue(mixed $value): mixed
    {
        return match (true) {
            $value === null, is_scalar($value) => $value,
            is_array($value) => $this->sanitize($value),
            $value instanceof BackedEnum => $value->value,
            $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
            is_object($value) => '[object '.$value::class.']',
            default => '[resource '.get_resource_type($value).']',
        };
    }

    private static function normalizeKey(string $key): string
    {
        return (string) preg_replace('/[^a-z0-9]/', '', strtolower($key));
    }
}
