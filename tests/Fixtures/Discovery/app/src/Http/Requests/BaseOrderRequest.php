<?php

declare(strict_types=1);

namespace EdfDiscoveryApp\Http\Requests;

use Edf\Laravel\Validation\HasErrorDefinitions;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseOrderRequest extends FormRequest
{
    use HasErrorDefinitions;
}
