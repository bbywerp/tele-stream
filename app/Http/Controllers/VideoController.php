<?php

namespace App\Http\Controllers;

use App\Http\Requests\VideoRequest;
use App\Models\Series;
use App\Models\Video;

class VideoController extends Controller
{
    public function create(Series $series)
    {
        return view('videos.form', ['series' => $series, 'video' => new Video(['is_active' => true])]);
    }

    public function store(VideoRequest $request, Series $series)
    {
        $series->videos()->create($request->validated());

        return to_route('series.show', $series)->with('status', 'Video ditambahkan.');
    }

    public function edit(Series $series, Video $video)
    {
        abort_unless($video->series_id === $series->id, 404);

        return view('videos.form', compact('series', 'video'));
    }

    public function update(VideoRequest $request, Series $series, Video $video)
    {
        abort_unless($video->series_id === $series->id, 404);
        $video->update($request->validated());

        return to_route('series.show', $series)->with('status', 'Video diperbarui.');
    }

    public function destroy(Series $series, Video $video)
    {
        abort_unless($video->series_id === $series->id, 404);
        $video->delete();

        return to_route('series.show', $series)->with('status', 'Video dihapus.');
    }
}
