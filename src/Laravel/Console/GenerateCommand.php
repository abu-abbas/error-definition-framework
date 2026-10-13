<?php

declare(strict_types=1);

namespace Edf\Laravel\Console;

use Edf\Core\Discovery\DiscoveryException;
use Edf\Core\Generation\ArtifactWriter;
use Edf\Core\Generation\GenerationException;
use Edf\Laravel\Generation\ArtifactGenerator;
use Illuminate\Console\Command;

/**
 * Menghasilkan generated artifact dari Error Definition PHP.
 *
 * @see docs/reference/artifact-generation.md#artisan-command
 */
final class GenerateCommand extends Command
{
    use PrintsLintFindings;

    protected $signature = 'error-definition:generate
        {--check : Periksa artifact tanpa menulis file}';

    protected $description = 'Menghasilkan artifact error code dari Error Definition';

    public function handle(ArtifactGenerator $generator, ArtifactWriter $writer): int
    {
        try {
            $artifacts = $generator->artifacts();

            return $this->option('check')
                ? $this->check($artifacts, $writer)
                : $this->write($artifacts, $writer);
        } catch (GenerationException $e) {
            if ($e->findings !== []) {
                $this->printFindings($e->findings);
                $this->newLine();
            }

            $this->error($e->getMessage());
        } catch (DiscoveryException $e) {
            $this->error($e->getMessage());
        }

        return self::FAILURE;
    }

    /**
     * @param array<string, string> $artifacts
     */
    private function write(array $artifacts, ArtifactWriter $writer): int
    {
        foreach ($artifacts as $path => $content) {
            $this->line(sprintf('%-10s %s', $writer->write($path, $content) ? 'generated' : 'unchanged', $this->display($path)));
        }

        return self::SUCCESS;
    }

    /**
     * @param array<string, string> $artifacts
     */
    private function check(array $artifacts, ArtifactWriter $writer): int
    {
        $stale = 0;

        foreach ($artifacts as $path => $content) {
            $upToDate = $writer->isUpToDate($path, $content);
            $stale += $upToDate ? 0 : 1;

            $this->line(sprintf('%-11s %s', $upToDate ? 'up to date' : 'stale', $this->display($path)));
        }

        if ($stale === 0) {
            return self::SUCCESS;
        }

        $this->newLine();
        $this->line(sprintf('%d stale %s', $stale, $stale === 1 ? 'artifact' : 'artifacts'));

        return self::FAILURE;
    }

    private function display(string $path): string
    {
        $base = rtrim($this->laravel->basePath(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return str_starts_with($path, $base) ? substr($path, strlen($base)) : $path;
    }
}
