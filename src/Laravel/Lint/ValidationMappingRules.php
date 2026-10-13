<?php

declare(strict_types=1);

namespace Edf\Laravel\Lint;

use BackedEnum;
use Closure;
use Edf\Core\ErrorCategory;
use Edf\Core\ErrorCode;
use Edf\Core\ErrorDefinitionReader;
use Edf\Core\Lint\LintFinding;
use Edf\Core\Lint\LintLevel;
use Edf\Laravel\Validation\ErrorCodeMap;
use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Container\Container;
use Illuminate\Contracts\Validation\CompilableRules;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ConditionalRules;
use Illuminate\Validation\Rules\Date;
use Illuminate\Validation\Rules\Numeric;
use Illuminate\Validation\Rules\StringRule;
use Illuminate\Validation\ValidationRuleParser;
use ReflectionClass;
use ReflectionMethod;
use Stringable;
use Throwable;

/**
 * Rule VAL001–VAL009 untuk FormRequest yang memakai HasErrorDefinitions.
 *
 * Rule dibaca dengan mengevaluasi rules() melalui container, sama seperti
 * FormRequest saat runtime. Rule dinamis tidak ditebak dan dilaporkan sebagai warning.
 *
 * @see docs/decisions/1-error-definition-framework/1.8-validator-dan-linter.md
 */
final class ValidationMappingRules
{
    /**
     * Rule yang tidak pernah gagal sehingga tidak membutuhkan mapping.
     */
    private const array MODIFIERS = [
        'bail', 'sometimes', 'nullable',
        'exclude', 'exclude_if', 'exclude_unless', 'exclude_with', 'exclude_without',
    ];

    /** @var list<LintFinding> */
    private array $findings = [];

    public function __construct(
        private readonly Container $container,
        private readonly ErrorDefinitionReader $reader,
    ) {}

    /**
     * @param iterable<mixed> $formRequests
     * @return list<LintFinding>
     */
    public function check(iterable $formRequests): array
    {
        $this->findings = [];
        $checked = [];

        foreach ($formRequests as $target) {
            if (is_string($target) && isset($checked[$target])) {
                continue;
            }

            if (! is_string($target) || ! class_exists($target)
                || ! is_subclass_of($target, FormRequest::class)
                || ! in_array(HasErrorDefinitions::class, class_uses_recursive($target), true)) {
                $this->error('VAL001', is_string($target) ? $target : get_debug_type($target),
                    'Target bukan FormRequest yang menggunakan HasErrorDefinitions.');

                continue;
            }

            $checked[$target] = true;
            $this->checkRequest($target);
        }

        return $this->findings;
    }

    /**
     * @param class-string<FormRequest> $class
     */
    private function checkRequest(string $class): void
    {
        $traitFile = (new ReflectionClass(HasErrorDefinitions::class))->getFileName();

        if ((new ReflectionMethod($class, 'messages'))->getFileName() !== $traitFile) {
            $this->error('VAL009', $class, 'FormRequest yang memakai HasErrorDefinitions tidak boleh meng-override messages().');
        }

        try {
            /** @var FormRequest $request */
            $request = $this->container->build($class);
            $request->setContainer($this->container);
        } catch (Throwable $e) {
            $this->warning('VAL002', $class, 'FormRequest tidak dapat dibuat: '.$e->getMessage());

            return;
        }

        $mapping = $this->evaluate($class, 'errorCodes()', fn (): mixed => $request->errorCodes());
        $rules = $this->evaluate($class, 'rules()', fn (): mixed => method_exists($request, 'rules')
            ? $this->container->call([$request, 'rules'])
            : []);

        $mapping = $mapping === null ? null : $this->checkMapping($class, $mapping);

        if ($mapping === null || $rules === null) {
            return;
        }

        $this->compare($class, $mapping, $rules);
    }

    /**
     * @param Closure(): mixed $callback
     * @return array<array-key, mixed>|null
     */
    private function evaluate(string $class, string $method, Closure $callback): ?array
    {
        try {
            $result = $callback();
        } catch (Throwable $e) {
            $this->warning('VAL002', $class, "{$method} tidak dapat dievaluasi: ".$e->getMessage());

            return null;
        }

        if (! is_array($result)) {
            $this->warning('VAL002', $class, "{$method} tidak mengembalikan array.");

            return null;
        }

        return $result;
    }

    /**
     * Memeriksa VAL003, VAL004, dan VAL008. Mengembalikan mapping dengan key yang valid.
     *
     * @param array<array-key, mixed> $mapping
     * @return array<string, mixed>
     */
    private function checkMapping(string $class, array $mapping): array
    {
        $valid = [];

        foreach ($mapping as $key => $error) {
            $source = $class.'['.$key.']';

            if (! is_string($key) || ! $this->isMappingKey($key)) {
                $this->error('VAL003', $source, 'Mapping key harus berbentuk attribute.rule.');
            } else {
                $valid[$key] = $error;
            }

            if (! $error instanceof ErrorCode || ! $error instanceof BackedEnum) {
                $this->error('VAL004', $source, 'Mapping value harus berupa enum ErrorCode dan BackedEnum.');

                continue;
            }

            try {
                $definition = $this->reader->read($error);
            } catch (Throwable $e) {
                $this->error('VAL004', $source, 'Mapping value tidak memiliki Error Definition yang valid: '.$e->getMessage());

                continue;
            }

            if ($definition->category !== ErrorCategory::VALIDATION
                || $definition->httpStatus !== 422
                || $definition->retryable !== false) {
                $this->error('VAL008', $source,
                    "Error {$definition->code} harus memiliki category validation, HTTP status 422, dan retryable false.");
            }
        }

        return $valid;
    }

    /**
     * Rule built-in memakai nama snake_case; custom Rule object memakai FQCN.
     */
    private function isMappingKey(string $key): bool
    {
        $position = strrpos($key, '.');

        if ($position === false || $position === 0) {
            return false;
        }

        $attribute = substr($key, 0, $position);
        $rule = substr($key, $position + 1);

        if (in_array('', explode('.', $attribute), true)) {
            return false;
        }

        return preg_match('/^[a-z][a-z0-9_]*$/', $rule) === 1 || class_exists($rule);
    }

    /**
     * Memeriksa VAL005, VAL006, dan VAL007.
     *
     * @param array<string, mixed> $mapping
     * @param array<array-key, mixed> $rules
     */
    private function compare(string $class, array $mapping, array $rules): void
    {
        $ruleKeys = [];
        $dynamicAttributes = [];

        foreach ($rules as $attribute => $attributeRules) {
            $attribute = (string) $attribute;

            foreach ($this->identifiers($attributeRules) as $identifier) {
                if ($identifier === null) {
                    $dynamicAttributes[] = $attribute;
                    $this->warning('VAL007', $class,
                        "Rule dinamis pada attribute {$attribute} tidak dapat diaudit secara penuh.");

                    continue;
                }

                $key = $attribute.'.'.$identifier;
                $ruleKeys[] = $key;

                if (ErrorCodeMap::matchingKey($mapping, $key) === null) {
                    $this->error('VAL005', $class.'['.$key.']',
                        "Rule {$identifier} pada attribute {$attribute} tidak memiliki mapping.");
                }
            }
        }

        foreach (array_keys($mapping) as $pattern) {
            if ($this->hasMatch($pattern, $ruleKeys)) {
                continue;
            }

            // Mapping untuk attribute dengan rule dinamis sudah tercakup oleh VAL007.
            $attribute = substr($pattern, 0, (int) strrpos($pattern, '.'));

            if ($this->hasMatch($attribute, $dynamicAttributes)) {
                continue;
            }

            $this->warning('VAL006', $class.'['.$pattern.']', 'Mapping tidak memiliki pasangan rule yang terdeteksi.');
        }
    }

    /**
     * @param list<string> $keys
     */
    private function hasMatch(string $pattern, array $keys): bool
    {
        foreach ($keys as $key) {
            if (ErrorCodeMap::matches($pattern, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Mengubah rule satu attribute menjadi identifier seperti yang tercatat pada
     * Validator::failed(). `null` menandakan rule dinamis yang tidak dapat diaudit.
     *
     * @return list<string|null>
     */
    private function identifiers(mixed $rules): array
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        } elseif (! is_array($rules)) {
            $rules = [$rules];
        }

        $identifiers = [];

        foreach ($rules as $rule) {
            foreach ($this->identify($rule) as $identifier) {
                if (! in_array($identifier, self::MODIFIERS, true)) {
                    $identifiers[] = $identifier;
                }
            }
        }

        return $identifiers;
    }

    /**
     * Mengikuti ValidationRuleParser::prepareRule() dan explodeExplicitRule().
     *
     * @return list<string|null>
     */
    private function identify(mixed $rule): array
    {
        if ($rule instanceof Closure || $rule instanceof ConditionalRules || $rule instanceof CompilableRules) {
            return [null];
        }

        if ($rule instanceof InvokableRule || $rule instanceof ValidationRule || $rule instanceof RuleContract) {
            return (new ReflectionClass($rule))->isAnonymous() ? [null] : [$rule::class];
        }

        if ($rule instanceof Date || $rule instanceof Numeric || $rule instanceof StringRule) {
            return array_merge(...array_map(fn (string $part): array => $this->identify($part), explode('|', (string) $rule)));
        }

        if ($rule instanceof Stringable) {
            $rule = (string) $rule;
        }

        if (! is_string($rule) && ! is_array($rule)) {
            return [null];
        }

        if ($rule === '' || $rule === []) {
            return [];
        }

        [$name] = ValidationRuleParser::parse($rule);

        return is_string($name) && $name !== '' ? [Str::snake($name)] : [];
    }

    private function error(string $ruleId, string $source, string $message): void
    {
        $this->findings[] = new LintFinding($ruleId, LintLevel::ERROR, $source, $message);
    }

    private function warning(string $ruleId, string $source, string $message): void
    {
        $this->findings[] = new LintFinding($ruleId, LintLevel::WARNING, $source, $message);
    }
}
