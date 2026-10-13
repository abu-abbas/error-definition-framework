<?php

declare(strict_types=1);

namespace EdfDiscoveryWarning;

use Closure;
use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Foundation\Http\FormRequest;

final class StoreShippingRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return [
            'address' => ['required'],
            'courier' => [fn (string $attribute, mixed $value, Closure $fail) => null],
        ];
    }

    public function errorCodes(): array
    {
        return ['address.required' => ShippingError::ADDRESS_REQUIRED];
    }
}
