<?php

namespace App\Http\Controllers;

use App\Models\CalendarExclusion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalendarController
{
    public function index(): View
    {
        $exclusions = CalendarExclusion::orderByDesc('starts_on')->orderByDesc('id')->paginate(25);

        return view('calendar.index', compact('exclusions'));
    }

    public function create(): View
    {
        return view('calendar.form', ['exclusion' => new CalendarExclusion]);
    }

    public function store(Request $request): RedirectResponse
    {
        CalendarExclusion::create($this->validatedExclusion($request));

        return to_route('calendar.index')->with('status', 'Exclusión registrada.');
    }

    public function edit(CalendarExclusion $exclusion): View
    {
        return view('calendar.form', compact('exclusion'));
    }

    public function update(Request $request, CalendarExclusion $exclusion): RedirectResponse
    {
        $exclusion->update($this->validatedExclusion($request));

        return to_route('calendar.index')->with('status', 'Exclusión actualizada.');
    }

    public function destroy(CalendarExclusion $exclusion): RedirectResponse
    {
        $exclusion->delete();

        return to_route('calendar.index')->with('status', 'Exclusión eliminada.');
    }

    private function validatedExclusion(Request $request): array
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(array_keys(CalendarExclusion::KINDS))],
            'description' => ['required', 'string', 'max:120'],
            'starts_on' => ['required', 'date_format:Y-m-d'],
            'ends_on' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:starts_on'],
        ]);

        $data['ends_on'] = $data['ends_on'] ?? $data['starts_on'];

        return $data;
    }
}
