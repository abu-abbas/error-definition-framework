<?php

declare(strict_types=1);

namespace Edf\Laravel\Console;

use Edf\Core\Lint\LintFinding;
use Edf\Core\Lint\LintLevel;

/**
 * Format finding yang sama untuk command lint dan generate.
 */
trait PrintsLintFindings
{
    /**
     * @param list<LintFinding> $findings
     */
    private function printFindings(array $findings): void
    {
        $errors = 0;
        $warnings = 0;

        foreach ($findings as $finding) {
            $finding->level === LintLevel::ERROR ? $errors++ : $warnings++;

            $this->line(sprintf('%-7s %s  %s', strtoupper($finding->level->value), $finding->ruleId, $finding->source));
            $this->line('        '.$finding->message);
            $this->newLine();
        }

        $this->line(sprintf(
            '%d %s, %d %s',
            $errors, $errors === 1 ? 'error' : 'errors',
            $warnings, $warnings === 1 ? 'warning' : 'warnings',
        ));
    }
}
