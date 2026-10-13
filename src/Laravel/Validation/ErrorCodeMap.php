<?php

declare(strict_types=1);

namespace Edf\Laravel\Validation;

/**
 * Pencocokan key `attribute.rule` terhadap mapping errorCodes(). Dipakai oleh
 * runtime (HasErrorDefinitions) dan linter agar keduanya memakai aturan yang sama.
 */
final class ErrorCodeMap
{
    /**
     * Mengembalikan mapping key yang dipakai untuk `$key`: exact match lebih dulu,
     * kemudian wildcard sesuai urutan mapping.
     *
     * @param array<array-key, mixed> $mapping
     */
    public static function matchingKey(array $mapping, string $key): ?string
    {
        if (array_key_exists($key, $mapping)) {
            return $key;
        }

        foreach (array_keys($mapping) as $pattern) {
            if (is_string($pattern) && str_contains($pattern, '*') && self::matches($pattern, $key)) {
                return $pattern;
            }
        }

        return null;
    }

    /**
     * Wildcard hanya mewakili satu segmen, sama seperti pencocokan custom message Laravel.
     */
    public static function matches(string $pattern, string $key): bool
    {
        if ($pattern === $key) {
            return true;
        }

        $regex = '#^'.str_replace('\*', '[^.]*', preg_quote($pattern, '#')).'\z#u';

        return preg_match($regex, $key) === 1;
    }
}
