<?php

declare(strict_types=1);

namespace Edf\Laravel\Console;

use Edf\Core\Lint\LintLevel;
use Edf\Laravel\Discovery\ErrorDefinitionDiscovery;
use Edf\Laravel\Lint\ErrorDefinitionLinter;
use Illuminate\Console\Command;

/**
 * Menjalankan Error Definition Linter terhadap seluruh target hasil discovery.
 *
 * @see docs/reference/error-definition-linter.md#artisan-command
 */
final class LintCommand extends Command
{
    protected $signature = 'error-definition:lint
        {--strict : Warning juga menghasilkan exit code 1}';

    protected $description = 'Memeriksa enum error dan validation mapping FormRequest';

    public function handle(ErrorDefinitionDiscovery $discovery, ErrorDefinitionLinter $linter): int
    {
        $targets = $discovery->discover();
        $report = $linter->lint($targets->errorEnums, $targets->formRequests);

        $errors = 0;
        $warnings = 0;

        foreach ($report->findings as $finding) {
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

        return $report->hasErrors() || ($this->option('strict') && $report->hasWarnings())
            ? self::FAILURE
            : self::SUCCESS;
    }
}
