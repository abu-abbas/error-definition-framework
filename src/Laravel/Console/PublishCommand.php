<?php

declare(strict_types=1);

namespace Edf\Laravel\Console;

use Edf\Core\Discovery\DiscoveryException;
use Edf\Core\Generation\GenerationException;
use Edf\Core\Publishing\PublishException;
use Edf\Core\Publishing\PublishPayload;
use Edf\Laravel\Generation\ArtifactGenerator;
use Edf\Laravel\Publishing\CatalogPublisher;
use Edf\Laravel\Publishing\DiskPublisher;
use Edf\Laravel\Publishing\HttpPublisher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository;

/**
 * Mengirim catalog lengkap aplikasi ke tujuan yang dipilih pada config.
 *
 * @see docs/decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md
 */
final class PublishCommand extends Command
{
    use PrintsLintFindings;

    protected $signature = 'error-definition:publish
        {--revision= : Source revision yang sedang di-build, misalnya commit SHA}';

    protected $description = 'Mengirim catalog Error Definition ke tujuan publish';

    public function handle(ArtifactGenerator $generator, Repository $config): int
    {
        try {
            $application = $config->get('error-definition.publish.application');
            $revision = $this->option('revision');

            // Kesalahan config terlihat sebelum discovery berjalan.
            PublishPayload::assertIdentity($application, $revision);
            $publisher = $this->publisher($config->get('error-definition.publish.driver'));

            $payload = PublishPayload::create($application, $revision, $generator->catalog());

            $destination = $publisher->publish($payload);
        } catch (GenerationException $e) {
            if ($e->findings !== []) {
                $this->printFindings($e->findings);
                $this->newLine();
            }

            $this->error($e->getMessage());

            return self::FAILURE;
        } catch (DiscoveryException|PublishException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->line(sprintf(
            'published  %s (%d %s, revision %s)',
            $destination,
            $count = count($payload->catalog['errors']),
            $count === 1 ? 'error' : 'errors',
            $payload->revision,
        ));

        return self::SUCCESS;
    }

    private function publisher(mixed $driver): CatalogPublisher
    {
        return match ($driver) {
            'disk' => $this->laravel->make(DiskPublisher::class),
            'http' => $this->laravel->make(HttpPublisher::class),
            default => throw PublishException::unsupportedDriver($driver),
        };
    }
}
