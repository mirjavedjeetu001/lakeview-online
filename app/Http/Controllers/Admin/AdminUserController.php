<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role')->toString());
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->with('branch:id,name')->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'role']),
            'branches' => Branch::orderBy('sort_order')->get(['id', 'name', 'is_active']),
            'permissions' => User::PERMISSION_LABELS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUser($request);
        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['permissions'] = $this->cleanPermissions($request->input('permissions', []), $validated['role']);
        $validated['branch_id'] = $validated['role'] === 'super_admin' ? null : ($validated['branch_id'] ?? null);

        User::create($validated);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, $user);

        if ($user->is($request->user()) && $validated['role'] === 'customer') {
            return redirect()->back()->withErrors(['role' => 'You cannot remove your own admin access.']);
        }

        if ($user->is($request->user()) && $request->user()->role === 'super_admin' && $validated['role'] !== 'super_admin') {
            return redirect()->back()->withErrors(['role' => 'The active super admin account cannot be downgraded from itself.']);
        }

        if (array_key_exists('password', $validated) && filled($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        if ($user->is($request->user())) {
            $validated['is_active'] = true;
        }
        $validated['permissions'] = $this->cleanPermissions($request->input('permissions', []), $validated['role']);
        $validated['branch_id'] = $validated['role'] === 'super_admin' ? null : ($validated['branch_id'] ?? null);

        $user->update($validated);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return redirect()->back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if (in_array($user->role, ['admin', 'super_admin'], true) && User::whereIn('role', ['admin', 'super_admin'])->where('is_active', true)->count() <= 1) {
            return redirect()->back()->withErrors(['user' => 'Keep at least one active admin account.']);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['nullable', 'required_without:email', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($user?->id)],
            'email' => ['nullable', 'required_without:phone', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => $user ? 'nullable|string|min:6' : 'required|string|min:6',
            'role' => 'required|in:customer,admin,super_admin',
            'is_active' => 'boolean',
            'branch_id' => 'nullable|exists:branches,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', User::ADMIN_PERMISSIONS),
        ]);
    }

    private function cleanPermissions(array $permissions, string $role): array
    {
        if ($role === 'super_admin') {
            return User::ADMIN_PERMISSIONS;
        }

        if ($role === 'customer') {
            return [];
        }

        return array_values(array_intersect($permissions, User::ADMIN_PERMISSIONS));
    }
}
