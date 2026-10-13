<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Lint;

use Illuminate\Foundation\Http\FormRequest;

final class PlainRequest extends FormRequest
{
    public function rules(): array
    {
        return ['nama' => ['required']];
    }
}
