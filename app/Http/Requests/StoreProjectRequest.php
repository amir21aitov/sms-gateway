<?php

namespace App\Http\Requests;

use App\Enums\HttpCode;
use App\Enums\SmsProvider;
use App\Helpers\ResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Enum;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'provider' => ['required', 'string', new Enum(SmsProvider::class)],
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
