<?php

namespace App\Http\Requests;

use App\Rules\HttpUrl;
use Illuminate\Foundation\Http\FormRequest;

class SeriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'cover_image' => ['nullable', 'string', 'max:2048', new HttpUrl],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
