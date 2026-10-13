<?php

declare(strict_types=1);

namespace Edf\Core\Lint;

/**
 * Satu pelanggaran yang ditemukan linter.
 *
 * Source berupa class, enum case (`Enum::CASE`), atau mapping key
 * (`Request[attribute.rule]`).
 */
final readonly class LintFinding
{
    public function __construct(
        public string $ruleId,
        public LintLevel $level,
        public string $source,
        public string $message,
    ) {}
}
