<?php

declare(strict_types=1);

namespace Edf\Tests\Unit;

use Edf\Core\ErrorCategory;
use Edf\Core\ErrorSeverity;
use Edf\Core\Generation\ErrorCatalogJsonGenerator;
use Edf\Core\Lint\ErrorDefinitionRules;
use Edf\Core\Publishing\PublishPayload;
use Edf\Core\ResolvedErrorDefinition;
use PHPUnit\Framework\TestCase;

/**
 * Menjaga JSON Schema yang dibawa package tetap sama dengan aturan linter,
 * generator, dan publish payload (Decision 2.2).
 */
final class JsonSchemaTest extends TestCase
{
    public function test_catalog_schema_matches_generator_and_linter(): void
    {
        $schema = $this->schema('error-catalog.v1.json');
        $error = $schema['properties']['errors']['items'];

        $this->assertSame(ErrorCatalogJsonGenerator::SCHEMA_VERSION, $schema['properties']['schemaVersion']['const']);
        $this->assertSame($this->pattern(ErrorDefinitionRules::CODE_FORMAT), $error['properties']['code']['pattern']);
        $this->assertSame(array_column(ErrorCategory::cases(), 'value'), $error['properties']['category']['enum']);
        $this->assertSame(array_column(ErrorSeverity::cases(), 'value'), $error['properties']['severity']['enum']);

        $generated = (new ErrorCatalogJsonGenerator())->catalog([
            new ResolvedErrorDefinition('SM-WF-001', 'Draft sedang dikunci.', ErrorCategory::WORKFLOW, 409, ErrorSeverity::MEDIUM, false),
        ]);

        $this->assertSame(array_keys($generated), $schema['required']);
        $this->assertSame(array_keys($generated['errors'][0]), $error['required']);
    }

    public function test_publish_request_schema_matches_payload(): void
    {
        $schema = $this->schema('publish-request.v1.json');
        $payload = PublishPayload::create('surat-masuk', 'a1b2c3d', ['schemaVersion' => 1, 'errors' => []]);

        $this->assertSame(array_keys($payload->toArray()), $schema['required']);
        $this->assertSame($this->pattern(PublishPayload::APPLICATION_FORMAT), $schema['properties']['application']['pattern']);
        $this->assertSame(PublishPayload::MAX_REVISION_LENGTH, $schema['properties']['revision']['maxLength']);
        $this->assertSame('error-catalog.v1.json', $schema['properties']['catalog']['$ref']);
    }

    /**
     * @return array<string, mixed>
     */
    private function schema(string $file): array
    {
        return json_decode(
            file_get_contents(dirname(__DIR__, 2).'/resources/schemas/'.$file),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
    }

    /**
     * Regex PHP '/.../' menjadi pattern JSON Schema tanpa delimiter.
     */
    private function pattern(string $regex): string
    {
        return substr($regex, 1, -1);
    }
}
