<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Validation;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class Uppercase implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strtoupper((string) $value) !== $value) {
            $fail('Pesan bawaan rule yang seharusnya tidak dipakai.');
        }
    }
}
