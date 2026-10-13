<?php

declare(strict_types=1);

namespace Edf\Core\Discovery;

use RuntimeException;
use Throwable;

/**
 * Discovery gagal; hasil parsial tidak dikembalikan agar linter dan generator
 * tidak terlihat berhasil padahal sebagian target terlewat.
 */
final class DiscoveryException extends RuntimeException
{
    public static function composerJsonNotFound(string $path): self
    {
        return new self("Root composer.json tidak ditemukan: {$path}.");
    }

    public static function invalidComposerJson(string $path, string $reason): self
    {
        return new self("Root composer.json tidak valid ({$path}): {$reason}.");
    }

    public static function unreadablePath(string $mapping, string $path): self
    {
        return new self("Autoload path {$mapping} tidak dapat dibaca: {$path}.");
    }

    public static function candidateFailed(string $class, string $path, Throwable $previous): self
    {
        return new self(
            "Composer autoloader gagal memuat {$class} ({$path}): {$previous->getMessage()}",
            previous: $previous,
        );
    }
}
