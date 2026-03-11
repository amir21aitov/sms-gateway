<?php

namespace App\Http\Requests;

use App\Enums\HttpCode;
use App\Enums\SmsStatus;
use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SmsHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(array_column(SmsStatus::cases(), 'value'))],
            'phone' => ['nullable', 'string', 'regex:/^\+998\d{9}$/'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ResponseHelper::response([
                'errors' => $validator->errors()
                ], HttpCode::UNPROCESSABLE_ENTITY,
            )
        );
    }
}
