<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if (auth()->user()->isSuperAdmin()) {
            // all users
        } elseif (auth()->user()->isAdmin()) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        } else {
            abort(403);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $tenants = Tenant::all();
        return view('users.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin,teacher,solo_teacher,student',
            'tenant_id' => 'nullable|exists:tenants,id',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return response()->json(['message' => 'User created successfully!', 'redirect' => route('users.index')]);
    }

    public function show(User $user)
    {
        $user->load(['tenant', 'courses', 'enrollments.course']);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $tenants = Tenant::all();
        return view('users.edit', compact('user', 'tenants'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,admin,teacher,solo_teacher,student',
            'tenant_id' => 'nullable|exists:tenants,id',
            'phone' => 'nullable|string',
            'bio' => 'nullable|string',
            'status' => 'required|in:active,suspended,inactive',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return response()->json(['message' => 'User updated successfully!']);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully!']);
    }
}
