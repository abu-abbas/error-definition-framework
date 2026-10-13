<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PlainOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return ['customer' => ['required']];
    }
}
