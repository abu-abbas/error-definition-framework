# Error Definition Framework

Error Definition Framework is a PHP library for defining, resolving, and standardizing application errors.

The project embraces modern PHP features such as enums, attributes, readonly objects, and reflection while following design principles inspired by Laravel. The goal is to provide a familiar developer experience without introducing a completely new programming model.

Instead of scattering error codes, messages, HTTP status codes, and metadata throughout an application, Error Definition Framework encourages defining every application error in a single, strongly typed source of truth.

## Philosophy

The framework is built around a few core principles:

* **Single Source of Truth** — Every application error is defined once.
* **Strongly Typed** — Error codes are represented by enums instead of magic strings.
* **Metadata Driven** — Error behavior is described using PHP Attributes.
* **Framework Friendly** — Designed to integrate naturally with Laravel while remaining independent from it.
* **Convention over Configuration** — Favor sensible defaults over excessive configuration.
* **Developer Experience First** — Keep APIs simple, predictable, and easy to maintain.

## Why?

Many applications eventually suffer from problems such as:

* Duplicated error codes.
* Inconsistent API responses.
* Scattered validation messages.
* Different exception formats across modules.
* Difficult-to-maintain documentation.

Error Definition Framework addresses these issues by introducing a single definition that can be reused throughout the application.

## Example

```php
enum SuratMasukError: string implements ErrorCode
{
    #[ErrorDefinition(
        message: 'Draft is currently locked.',
        category: ErrorCategory::BUSINESS,
        httpStatus: 409,
    )]
    case DRAFT_LOCKED = 'SM-WF-001';
}
```

Resolving an error definition is straightforward:

```php
$definition = $reader->read(
    SuratMasukError::DRAFT_LOCKED
);

$definition->code;
$definition->message;
$definition->httpStatus;
```

## Roadmap

The framework is being developed incrementally.

Current roadmap:

1. Error Definition Framework

   * Error Definition
   * Error Definition Reader
   * Application Exception
   * Laravel Validation Integration
   * Standard JSON Response
   * Logging & Context
   * Error Linter
   * TypeScript Error Code Generator
   * Error Catalog Generator

Future roadmap:

* Central Error Registry
* Knowledge Base Integration
* ITSM Integration
* Error Analytics
