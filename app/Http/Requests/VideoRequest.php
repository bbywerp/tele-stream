<?php

namespace App\Http\Requests;

use App\Rules\HttpUrl;
use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
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
            'thumbnail_url' => ['nullable', 'string', 'max:2048', new HttpUrl],
            'stream_url' => ['required', 'string', 'max:8192', new HttpUrl],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
