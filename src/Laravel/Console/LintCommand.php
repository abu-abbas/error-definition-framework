<?php

declare(strict_types=1);

namespace Edf\Laravel\Console;

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
    use PrintsLintFindings;

    protected $signature = 'error-definition:lint
        {--strict : Warning juga menghasilkan exit code 1}';

    protected $description = 'Memeriksa enum error dan validation mapping FormRequest';

    public function handle(ErrorDefinitionDiscovery $discovery, ErrorDefinitionLinter $linter): int
    {
        $targets = $discovery->discover();
        $report = $linter->lint($targets->errorEnums, $targets->formRequests);

        $this->printFindings($report->findings);

        return $report->hasErrors() || ($this->option('strict') && $report->hasWarnings())
            ? self::FAILURE
            : self::SUCCESS;
    }
}
