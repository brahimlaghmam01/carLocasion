<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AgenciesController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();
        $cityId = $request->input('city_id');

        $agencies = Agency::query()
            ->with('city:id,name')
            ->withCount(['cars', 'reservations', 'users'])
            ->when($search, fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($cityId, fn ($q) => $q->where('city_id', $cityId))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Agencies/Index', [
            'agencies' => $agencies,
            'cities' => City::orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search, 'city_id' => $cityId],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAgency($request);

        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        Agency::create($validated);

        return redirect()->route('admin.agencies.index')->with('success', 'Agency created successfully.');
    }

    public function update(Request $request, Agency $agency)
    {
        $validated = $this->validateAgency($request);

        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $agency->update($validated);

        return redirect()->route('admin.agencies.index')->with('success', 'Agency updated successfully.');
    }

    public function destroy(Agency $agency)
    {
        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $agency->delete();

        return redirect()->route('admin.agencies.index')->with('success', 'Agency deleted successfully.');
    }

    private function validateAgency(Request $request): array
    {
        return $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);
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
