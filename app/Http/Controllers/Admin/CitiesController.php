<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CitiesController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $cities = City::query()
            ->withCount('agencies')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Cities/Index', [
            'cities' => $cities,
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:cities,name'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        City::create($validated);

        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }

    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->ignore($city->id)],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $city->update($validated);

        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }

    private function blockedByDemo(): bool
    {
        return (bool) config('app.demo');
    }

    private function demoResponse()
    {
        return redirect()->back()->with(
            'restricted_action',
            'This is a demo version. For security reasons, create, update, and delete actions are disabled.'
        );
    }
}
