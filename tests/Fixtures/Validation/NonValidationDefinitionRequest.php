<?php

declare(strict_types=1);

namespace Edf\Tests\Fixtures\Validation;

use Edf\Laravel\Validation\HasErrorDefinitions;
use Edf\Tests\Fixtures\SuratMasukError;
use Illuminate\Foundation\Http\FormRequest;

final class NonValidationDefinitionRequest extends FormRequest
{
    use HasErrorDefinitions;

    public function rules(): array
    {
        return ['draft' => ['required']];
    }

    public function errorCodes(): array
    {
        return ['draft.required' => SuratMasukError::DRAFT_LOCKED];
    }
}
