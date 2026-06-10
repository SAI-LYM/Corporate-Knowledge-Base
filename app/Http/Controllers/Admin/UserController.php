<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Admin user management (CLAUDE.md §1) — assign a user's role + department.
 * Every action is gated by UserPolicy (defence in depth behind role:Admin).
 * Only the two authorization axes are editable; name/email/password are never
 * touched here (no mass-assignment surface).
 */
class UserController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::with(['role', 'department'])
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('rank')->get(),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $user->update($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Updated {$user->name}'s role & department.");
    }
}
