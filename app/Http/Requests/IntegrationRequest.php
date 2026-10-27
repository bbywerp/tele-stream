<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IntegrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return ['api_token' => ['nullable', 'string', 'min:32', 'max:200', 'regex:/\A[A-Za-z0-9_-]+\z/']];
    }
}
