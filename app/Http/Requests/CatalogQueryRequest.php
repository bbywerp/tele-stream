<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogQueryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Server-to-server authorization is enforced by AuthenticateApplication middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'eligible' => ['nullable', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
            'exclude' => ['nullable', 'array', 'max:500'],
            'exclude.*' => ['uuid'],
        ];
    }
}
