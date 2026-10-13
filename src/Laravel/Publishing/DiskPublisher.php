<?php

declare(strict_types=1);

namespace Edf\Laravel\Publishing;

use Edf\Core\Publishing\PublishException;
use Edf\Core\Publishing\PublishPayload;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;

/**
 * Menulis payload sebagai file JSON ke filesystem disk Laravel. File ditimpa
 * pada setiap publish sehingga selalu berisi catalog terakhir.
 */
final class DiskPublisher implements CatalogPublisher
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly Repository $config,
    ) {}

    public function publish(PublishPayload $payload): string
    {
        $disk = $this->string('publish.disk.name');
        $path = str_replace('{application}', $payload->application, $this->string('publish.disk.path'));
        $destination = "{$disk}:{$path}";

        if (! $this->filesystem->disk($disk)->put($path, $payload->toJson())) {
            throw PublishException::writeFailed($destination);
        }

        return $destination;
    }

    private function string(string $key): string
    {
        $value = $this->config->get('error-definition.'.$key);

        if (! is_string($value) || $value === '') {
            throw PublishException::missingConfig($key);
        }

        return $value;
    }
}
