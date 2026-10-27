<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HttpUrl implements ValidationRule
{
    public static function valid(?string $value): bool
    {
        if (! $value || preg_match('/[\x00-\x20\x7f\\\\]/', $value)) {
            return false;
        }
        $parts = parse_url($value);

        return filter_var($value, FILTER_VALIDATE_URL) !== false
            && is_array($parts)
            && in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            && ! empty($parts['host'])
            && ! isset($parts['user']) && ! isset($parts['pass']);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! self::valid($value)) {
            $fail('Gunakan URL HTTP/HTTPS yang valid tanpa kredensial.');
        }
    }
}
