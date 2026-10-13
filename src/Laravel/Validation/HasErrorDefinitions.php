<?php

declare(strict_types=1);

namespace Edf\Laravel\Validation;

use BackedEnum;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorDefinitionReader;
use Edf\Core\ResolvedErrorDefinition;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as LaravelValidator;

/**
 * Opt-in FormRequest untuk memetakan setiap validation failure ke Error Definition.
 *
 * Message validation berasal dari Error Definition; placeholder seperti :attribute
 * tetap diproses oleh Laravel. Request yang memakai trait ini tidak boleh
 * mendefinisikan messages() sendiri.
 *
 * @see docs/decisions/1-error-definition-framework/1.5-validation-error-mapping.md
 */
trait HasErrorDefinitions
{
    /**
     * Mapping dari addValidationError(), dengan key `attribute.rule` konkret.
     *
     * @var array<string, ErrorCode&BackedEnum>
     */
    private array $manualErrorCodes = [];

    /**
     * Mapping `attribute.rule` ke enum error. Attribute mendukung dot notation
     * dan wildcard; custom Rule object menggunakan FQCN sebagai rule.
     *
     * @return array<string, ErrorCode&BackedEnum>
     */
    abstract public function errorCodes(): array;

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_map(
            fn (ErrorCode&BackedEnum $error): string => $this->resolveValidationDefinition($error)->message,
            $this->errorCodes(),
        );
    }

    /**
     * Menambahkan validation error manual, misalnya dari after(), lengkap dengan
     * attribute, rule identifier, dan Error Definition.
     */
    protected function addValidationError(
        LaravelValidator $validator,
        string $attribute,
        string $rule,
        ErrorCode&BackedEnum $error,
    ): void {
        $key = $attribute.'.'.$rule;

        $this->manualErrorCodes[$key] = $error;

        $validator->setCustomMessages([
            $key => $this->resolveValidationDefinition($error)->message,
        ]);

        $validator->addFailure($attribute, $rule);
    }

    /**
     * @param LaravelValidator $validator
     */
    protected function failedValidation(Validator $validator): void
    {
        $resolvedErrors = [];

        foreach ($validator->failed() as $attribute => $rules) {
            foreach ($rules as $rule => $parameters) {
                $identifier = $this->validationRuleIdentifier($rule);
                $definition = $this->resolveValidationDefinition(
                    $this->findErrorCode($attribute, $identifier),
                );

                $resolvedErrors[] = new ResolvedValidationError(
                    attribute: $attribute,
                    rule: $identifier,
                    definition: $definition,
                    message: $validator->makeReplacements($definition->message, $attribute, $rule, $parameters),
                );
            }
        }

        throw (new ErrorValidationException($validator, $resolvedErrors))
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    /**
     * Built-in rule memakai nama Laravel (snake_case); custom Rule object memakai FQCN.
     */
    private function validationRuleIdentifier(string $rule): string
    {
        return class_exists($rule) ? $rule : Str::snake($rule);
    }

    private function findErrorCode(string $attribute, string $rule): ErrorCode&BackedEnum
    {
        $key = $attribute.'.'.$rule;
        $mapping = [...$this->errorCodes(), ...$this->manualErrorCodes];

        if (isset($mapping[$key])) {
            return $mapping[$key];
        }

        foreach ($mapping as $pattern => $error) {
            if (! str_contains($pattern, '*')) {
                continue;
            }

            // Wildcard hanya mewakili satu segmen, sama seperti pencocokan custom message Laravel.
            $regex = '#^'.str_replace('\*', '[^.]*', preg_quote($pattern, '#')).'\z#u';

            if (preg_match($regex, $key) === 1) {
                return $error;
            }
        }

        throw MissingValidationErrorCodeException::for(static::class, $attribute, $rule);
    }

    private function resolveValidationDefinition(ErrorCode&BackedEnum $error): ResolvedErrorDefinition
    {
        $definition = $this->container->make(ErrorDefinitionReader::class)->read($error);

        if ($definition->category !== ErrorCategory::VALIDATION
            || $definition->httpStatus !== 422
            || $definition->retryable !== false) {
            throw InvalidValidationErrorDefinitionException::for($definition);
        }

        return $definition;
    }
}
