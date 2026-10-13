<?php

declare(strict_types=1);

namespace Edf\Laravel\Publishing;

use Edf\Core\Publishing\PublishException;
use Edf\Core\Publishing\PublishPayload;

/**
 * Driver tujuan publish catalog.
 *
 * @see docs/decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md#driver
 */
interface CatalogPublisher
{
    /**
     * @return string tujuan yang ditampilkan pada output command
     *
     * @throws PublishException
     */
    public function publish(PublishPayload $payload): string;
}
