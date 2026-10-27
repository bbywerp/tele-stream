<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesFilterRequest;
use App\Http\Requests\SeriesRequest;
use App\Models\Series;

class SeriesController extends Controller
{
    public function index(SeriesFilterRequest $request)
    {
        $search = $request->validated('search') ?? '';
        $series = Series::withCount('videos')->when($search, fn ($q) => $q->where('title', 'like', '%'.$search.'%'))->latest()->paginate(15)->withQueryString();

        return view('series.index', compact('series', 'search'));
    }

    public function create()
    {
        return view('series.form', ['series' => new Series(['is_active' => true])]);
    }

    public function store(SeriesRequest $request)
    {
        $series = Series::create($request->validated());

        return to_route('series.show', $series)->with('status', 'Series ditambahkan.');
    }

    public function show(Series $series)
    {
        return view('series.show', ['series' => $series, 'videos' => $series->videos()->latest()->paginate(15)]);
    }

    public function edit(Series $series)
    {
        return view('series.form', compact('series'));
    }

    public function update(SeriesRequest $request, Series $series)
    {
        $series->update($request->validated());

        return to_route('series.show', $series)->with('status', 'Series diperbarui.');
    }

    public function destroy(Series $series)
    {
        $series->delete();

        return to_route('series.index')->with('status', 'Series dan video di dalamnya dihapus.');
    }
}
