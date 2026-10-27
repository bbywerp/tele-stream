<?php

namespace App\Http\Controllers;

use App\Http\Requests\CatalogQueryRequest;
use App\Models\Series;
use App\Models\Video;

class CatalogApiController extends Controller
{
    private function success(mixed $data, array $meta = [])
    {
        return response()->json(['success' => true, 'data' => $data, 'error' => null] + $meta);
    }

    public function stats()
    {
        return $this->success(['series_count' => Series::where('is_active', true)->count(), 'eligible_count' => Series::eligible()->count()]);
    }

    public function index(CatalogQueryRequest $request)
    {
        $page = Series::where('is_active', true)->when($request->boolean('eligible'), fn ($q) => $q->eligible())
            ->select('uuid', 'title', 'description', 'cover_image')->orderBy('id')->paginate(50);

        return $this->success($page->items(), ['meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]]);
    }

    public function random(CatalogQueryRequest $request)
    {
        $data = $request->validated();
        $series = Series::eligible()->whereNotIn('uuid', $data['exclude'] ?? [])->inRandomOrder()->firstOrFail();

        return $this->success($series->only('uuid', 'title', 'description', 'cover_image'));
    }

    public function show(Series $series)
    {
        abort_unless($series->is_active, 404);

        return $this->success($series->only('uuid', 'title', 'description', 'cover_image'));
    }

    public function videos(CatalogQueryRequest $request, Series $series)
    {
        abort_unless($series->is_active, 404);
        $page = $series->videos()->where('is_active', true)->select('uuid', 'title', 'description', 'thumbnail_url')->orderBy('id')->paginate(50);

        return $this->success($page->items(), ['meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'total' => $page->total()]]);
    }

    public function video(Video $video)
    {
        abort_unless($video->is_active && $video->series->is_active, 404);

        return $this->success($video->only('uuid', 'title', 'description', 'thumbnail_url') + ['series_uuid' => $video->series->uuid, 'series_title' => $video->series->title]);
    }
}
