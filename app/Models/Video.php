<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Video extends Model
{
    protected $fillable = ['series_id', 'title', 'description', 'thumbnail_url', 'stream_url', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    protected $hidden = ['stream_url'];

    protected static function booted(): void
    {
        static::creating(fn (Video $video) => $video->uuid = (string) Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
