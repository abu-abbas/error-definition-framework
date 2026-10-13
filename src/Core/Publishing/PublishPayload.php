<?php

declare(strict_types=1);

namespace Edf\Core\Publishing;

use Edf\Core\Generation\JsonEncoder;

/**
 * Envelope publish: identitas aplikasi, source revision, dan catalog lengkap
 * yang identik dengan isi error-catalog.json.
 *
 * @see docs/decisions/2-publish-catalog/2.1-publish-catalog-dari-aplikasi.md#isi-kiriman
 * @see resources/schemas/publish-request.v1.json
 */
final readonly class PublishPayload
{
    public const string APPLICATION_FORMAT = '/^[a-z0-9]+(?:-[a-z0-9]+)*$/';

    public const int MAX_REVISION_LENGTH = 64;

    /**
     * @param array{schemaVersion: int, errors: list<array<string, mixed>>} $catalog
     */
    private function __construct(
        public string $application,
        public string $revision,
        public array $catalog,
    ) {}

    /**
     * @param array{schemaVersion: int, errors: list<array<string, mixed>>} $catalog
     */
    public static function create(mixed $application, mixed $revision, array $catalog): self
    {
        self::assertIdentity($application, $revision);

        /** @var string $application */
        /** @var string $revision */
        return new self($application, $revision, $catalog);
    }

    /**
     * Memeriksa identitas aplikasi dan revision tanpa membangun catalog, agar
     * kesalahan config terlihat sebelum discovery berjalan.
     *
     * @throws PublishException
     */
    public static function assertIdentity(mixed $application, mixed $revision): void
    {
        if (! is_string($application) || preg_match(self::APPLICATION_FORMAT, $application) !== 1) {
            throw PublishException::invalidApplication($application);
        }

        if (! is_string($revision) || trim($revision) === '' || strlen($revision) > self::MAX_REVISION_LENGTH) {
            throw PublishException::invalidRevision();
        }
    }

    /**
     * @return array{application: string, revision: string, catalog: array{schemaVersion: int, errors: list<array<string, mixed>>}}
     */
    public function toArray(): array
    {
        return [
            'application' => $this->application,
            'revision' => $this->revision,
            'catalog' => $this->catalog,
        ];
    }

    public function toJson(): string
    {
        return JsonEncoder::encode($this->toArray(), 'publish payload');
    }
}
