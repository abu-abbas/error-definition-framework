<?php

declare(strict_types=1);

namespace Edf\Laravel;

use Edf\Core\Exceptions\ApplicationException;
use Edf\Core\ResolvedErrorDefinition;
use Edf\Laravel\Validation\ErrorValidationException;
use Illuminate\Http\JsonResponse;

/**
 * Membentuk response JSON untuk exception yang membawa Error Definition.
 *
 * Category, severity, runtime context, rule validation, dan debug information
 * tidak pernah dikirim kepada client, termasuk ketika debug mode aktif.
 *
 * @see docs/decisions/1-error-definition-framework/1.6-format-response-json-standar.md
 */
final class ErrorResponseRenderer
{
    public function application(ApplicationException $exception): JsonResponse
    {
        return new JsonResponse(
            $this->publicFields($exception->definition, $exception->definition->message),
            $exception->definition->httpStatus,
        );
    }

    public function validation(ErrorValidationException $exception): JsonResponse
    {
        $errors = [];

        foreach ($exception->resolvedErrors as $error) {
            $errors[$error->attribute][] = $this->publicFields($error->definition, $error->message);
        }

        return new JsonResponse([
            'message' => $exception->getMessage(),
            'errors' => $errors,
        ], 422);
    }

    /**
     * @return array{message: string, code: string, retryable: bool}
     */
    private function publicFields(ResolvedErrorDefinition $definition, string $message): array
    {
        return [
            'message' => $message,
            'code' => $definition->code,
            'retryable' => $definition->retryable,
        ];
    }
}
