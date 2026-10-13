<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Http\Requests;

use EdfDiscoveryApp\Errors\OrderError;

final class UpdateOrderRequest extends BaseOrderRequest
{
    public function rules(): array
    {
        return ['customer' => ['required']];
    }

    public function errorCodes(): array
    {
        return ['customer.required' => OrderError::CUSTOMER_REQUIRED];
    }
}
