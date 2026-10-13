<?php

declare(strict_types=1);

namespace Edf\Laravel\Publishing;

use Edf\Core\Publishing\PublishException;
use Edf\Core\Publishing\PublishPayload;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as Http;

/**
 * Mengirim payload dengan POST ke URL yang dikonfigurasi, tanpa retry.
 * Bearer token hanya dikirim jika diisi.
 */
final class HttpPublisher implements CatalogPublisher
{
    public function __construct(
        private readonly Http $http,
        private readonly Repository $config,
    ) {}

    public function publish(PublishPayload $payload): string
    {
        $url = $this->config->get('error-definition.publish.http.url');

        if (! is_string($url) || $url === '') {
            throw PublishException::missingConfig('publish.http.url');
        }

        $token = $this->config->get('error-definition.publish.http.token');
        $request = $this->http
            ->acceptJson()
            ->timeout((int) $this->config->get('error-definition.publish.http.timeout', 10));

        if (is_string($token) && $token !== '') {
            $request = $request->withToken($token);
        }

        try {
            $response = $request
                ->withBody($payload->toJson(), 'application/json')
                ->post($url);
        } catch (ConnectionException $e) {
            throw PublishException::unreachable($url, $e);
        }

        if (! $response->successful()) {
            $message = $response->json('message');

            throw PublishException::rejected($response->status(), is_string($message) ? $message : null);
        }

        return $url;
    }
}
