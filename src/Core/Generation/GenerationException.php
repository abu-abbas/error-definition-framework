<?php

declare(strict_types=1);

namespace Edf\Core\Generation;

use Edf\Core\Lint\LintFinding;
use RuntimeException;
use Throwable;

/**
 * Generation gagal; tidak ada partial artifact yang ditulis secara sengaja.
 */
final class GenerationException extends RuntimeException
{
    /**
     * @param list<LintFinding> $findings temuan EDF yang menggagalkan generation
     */
    public function __construct(
        string $message,
        public readonly array $findings = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    /**
     * @param list<LintFinding> $findings
     */
    public static function lintFailed(array $findings): self
    {
        return new self('Error Definition memiliki lint error; artifact tidak dihasilkan.', $findings);
    }

    public static function resolveFailed(string $source, Throwable $previous): self
    {
        return new self("Error Definition {$source} gagal di-resolve: {$previous->getMessage()}", previous: $previous);
    }

    public static function encodingFailed(string $artifact, Throwable $previous): self
    {
        return new self("{$artifact} gagal di-encode: {$previous->getMessage()}", previous: $previous);
    }

    public static function invalidPath(string $configKey): self
    {
        return new self("Config {$configKey} harus berisi path file yang berbeda dari artifact lain.");
    }

    public static function unwritable(string $path, string $reason): self
    {
        return new self("Artifact {$path} tidak dapat ditulis: {$reason}.");
    }
}
