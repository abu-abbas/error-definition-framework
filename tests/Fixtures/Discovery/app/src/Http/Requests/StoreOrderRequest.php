<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Http\Requests;

use EdfDiscoveryApp\Errors\OrderError;
use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Foundation\Http\FormRequest;

final class StoreOrderRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return ['customer' => ['required']];
    }

    public function errorCodes(): array
    {
        return ['customer.required' => OrderError::CUSTOMER_REQUIRED];
    }
}
