<?php

declare(strict_types=1);

namespace Edf\Core\Publishing;

use RuntimeException;
use Throwable;

final class PublishException extends RuntimeException
{
    public static function invalidApplication(mixed $application): self
    {
        $value = is_string($application) && $application !== '' ? "'{$application}'" : 'kosong';

        return new self("Config error-definition.publish.application harus berupa slug huruf kecil seperti 'surat-masuk', saat ini {$value}.");
    }

    public static function invalidRevision(): self
    {
        return new self('Option --revision wajib diisi, maksimal 64 karakter, misalnya commit SHA.');
    }

    public static function unsupportedDriver(mixed $driver): self
    {
        $value = is_string($driver) ? "'{$driver}'" : 'kosong';

        return new self("Driver publish {$value} tidak dikenal. Gunakan 'disk' atau 'http'.");
    }

    public static function missingConfig(string $key): self
    {
        return new self("Config error-definition.{$key} wajib diisi untuk driver ini.");
    }

    public static function writeFailed(string $destination): self
    {
        return new self("Catalog gagal ditulis ke {$destination}.");
    }

    public static function rejected(int $status, ?string $message): self
    {
        $detail = $message !== null && $message !== '' ? ": {$message}" : '.';

        return new self("Penerima menolak catalog dengan HTTP {$status}{$detail}");
    }

    public static function unreachable(string $url, Throwable $previous): self
    {
        return new self("Penerima {$url} tidak dapat dihubungi: {$previous->getMessage()}", previous: $previous);
    }
}
