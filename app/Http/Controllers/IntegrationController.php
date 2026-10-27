<?php

namespace App\Http\Controllers;

use App\Http\Requests\IntegrationRequest;
use App\Models\IntegrationSetting;

class IntegrationController extends Controller
{
    public function edit()
    {
        return view('settings.api', ['configured' => (bool) IntegrationSetting::findOrFail(1)->api_token]);
    }

    public function update(IntegrationRequest $request)
    {
        if ($request->filled('api_token')) {
            IntegrationSetting::findOrFail(1)->update($request->validated());
        }

        return back()->with('status', 'Token API disimpan terenkripsi.');
    }
}
