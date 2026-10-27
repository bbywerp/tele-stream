<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Series extends Model
{
    protected $table = 'series';

    protected $fillable = ['title', 'description', 'cover_image', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(fn (Series $series) => $series->uuid = (string) Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function scopeEligible(Builder $query): void
    {
        $query->where('is_active', true)->whereHas('videos', fn ($q) => $q->where('is_active', true), '>=', 3);
    }
}
