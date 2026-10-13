<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\Publishing\PublishException;
use Edf\Core\Publishing\PublishPayload;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PublishPayloadTest extends TestCase
{
    public function test_wraps_catalog_without_changing_it(): void
    {
        $catalog = ['schemaVersion' => 1, 'errors' => []];
        $payload = PublishPayload::create('surat-masuk', 'a1b2c3d', $catalog);

        $this->assertSame(
            ['application' => 'surat-masuk', 'revision' => 'a1b2c3d', 'catalog' => $catalog],
            $payload->toArray(),
        );
        $this->assertSame(<<<'JSON'
            {
              "application": "surat-masuk",
              "revision": "a1b2c3d",
              "catalog": {
                "schemaVersion": 1,
                "errors": []
              }
            }

            JSON, $payload->toJson());
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidApplications(): iterable
    {
        yield 'kosong' => [null];
        yield 'string kosong' => [''];
        yield 'huruf kapital' => ['Surat-Masuk'];
        yield 'spasi' => ['surat masuk'];
        yield 'tanda hubung di akhir' => ['surat-'];
        yield 'bukan string' => [123];
    }

    #[DataProvider('invalidApplications')]
    public function test_rejects_invalid_application(mixed $application): void
    {
        $this->expectException(PublishException::class);
        $this->expectExceptionMessage('publish.application');

        PublishPayload::create($application, 'a1b2c3d', []);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidRevisions(): iterable
    {
        yield 'kosong' => [null];
        yield 'whitespace' => ['  '];
        yield 'terlalu panjang' => [str_repeat('a', 65)];
    }

    #[DataProvider('invalidRevisions')]
    public function test_rejects_invalid_revision(mixed $revision): void
    {
        $this->expectException(PublishException::class);
        $this->expectExceptionMessage('--revision');

        PublishPayload::create('surat-masuk', $revision, []);
    }

    public function test_accepts_revision_up_to_64_characters(): void
    {
        $revision = str_repeat('a', 64);

        $this->assertSame($revision, PublishPayload::create('kepegawaian', $revision, [])->revision);
    }
}
