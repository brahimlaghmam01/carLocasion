<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AgencyAdminsController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $admins = User::query()
            ->where('role', UserRole::AGENCY_ADMIN)
            ->with('agency:id,name')
            ->when($search, fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            }))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/AgencyAdmins/Index', [
            'admins' => $admins,
            'agencies' => Agency::orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'agency_id' => ['required', 'exists:agencies,id'],
        ]);

        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::AGENCY_ADMIN,
            'role_id' => $this->agencyAdminRoleId(),
            'agency_id' => $validated['agency_id'],
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->log('created', $user);

        return redirect()->route('admin.agency-admins.index')->with('success', 'Agency admin created successfully.');
    }

    public function update(Request $request, User $agency_admin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($agency_admin->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'agency_id' => ['required', 'exists:agencies,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $agency_admin->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'agency_id' => $validated['agency_id'],
            'is_active' => $validated['is_active'],
        ]);

        if (! empty($validated['password'])) {
            $agency_admin->password = Hash::make($validated['password']);
        }

        $agency_admin->save();
        $this->log('updated', $agency_admin);

        return redirect()->route('admin.agency-admins.index')->with('success', 'Agency admin updated successfully.');
    }

    public function destroy(User $agency_admin)
    {
        if ($this->blockedByDemo()) {
            return $this->demoResponse();
        }

        $this->log('deleted', $agency_admin);
        $agency_admin->delete();

        return redirect()->route('admin.agency-admins.index')->with('success', 'Agency admin deleted successfully.');
    }

    private function agencyAdminRoleId(): ?int
    {
        return Role::where('slug', 'agency-admin')->value('id');
    }

    private function log(string $action, User $user): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'agency_id' => $user->agency_id,
            'action' => $action,
            'model' => User::class,
            'record_id' => $user->id,
            'description' => ucfirst($action) . " agency admin #{$user->id}",
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
