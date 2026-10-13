<?php

declare(strict_types=1);

namespace Edf\Core\Lint;

/**
 * Seluruh temuan linter dalam urutan pemeriksaan.
 *
 * @see docs/reference/error-definition-linter.md
 */
final readonly class LintReport
{
    /** @param list<LintFinding> $findings */
    public function __construct(
        public array $findings,
    ) {}

    public function hasErrors(): bool
    {
        return $this->has(LintLevel::ERROR);
    }

    public function hasWarnings(): bool
    {
        return $this->has(LintLevel::WARNING);
    }

    private function has(LintLevel $level): bool
    {
        foreach ($this->findings as $finding) {
            if ($finding->level === $level) {
                return true;
            }
        }

        return false;
    }
}
