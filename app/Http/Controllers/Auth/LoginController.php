<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuthDriver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Show the login page: mock user-picker (dev) or the Microsoft button (oauth). */
    public function show(): View
    {
        $mockUsers = AuthDriver::mockEnabled()
            ? User::with(['role', 'department'])->orderBy('name')->get()
            : collect();

        return view('auth.login', [
            'mockEnabled' => AuthDriver::mockEnabled(),
            'oauthEnabled' => AuthDriver::isOauth(),
            'mockUsers' => $mockUsers,
        ]);
    }

    /**
     * Mock login (dev/demo only): log in a chosen seeded user without Azure.
     * Hard-blocked unless the mock driver is enabled AND we're not in production.
     */
    public function mock(Request $request): RedirectResponse
    {
        abort_unless(AuthDriver::mockEnabled(), 404);

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        Auth::loginUsingId((int) $data['user_id']);
        $request->session()->regenerate(); // prevent session fixation

        return redirect()->intended(route('dashboard'));
    }

    /** Log out and fully invalidate the session (CLAUDE.md §3 — working logout). */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
