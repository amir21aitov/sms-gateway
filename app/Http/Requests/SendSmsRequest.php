<?php

namespace App\Http\Requests;

use App\Enums\HttpCode;
use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phones' => ['required', 'array', 'min:1', 'max:500'],
            'phones.*' => ['required', 'string', 'regex:/^\+998\d{9}$/'],
            'message' => ['required', 'string', 'max:1600'],
        ];
    }

    public function messages(): array
    {
        return [
            'phones.*.regex' => 'Phone number must be in format +998XXXXXXXXX.',
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
