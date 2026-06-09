<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AzureUserProvisioner;
use App\Support\AuthDriver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirect;
use Throwable;

/**
 * Microsoft Entra ID (Azure AD) OAuth login via Socialite (CLAUDE.md §3).
 * Logins are restricted server-side to the configured company domain.
 */
class AzureController extends Controller
{
    /** Kick off the OAuth redirect to Microsoft. */
    public function redirect(): SymfonyRedirect
    {
        abort_unless(AuthDriver::isOauth(), 404);

        return Socialite::driver('azure')->redirect();
    }

    /** Handle the callback: verify domain, provision the user, log in. */
    public function callback(Request $request, AzureUserProvisioner $provisioner): RedirectResponse
    {
        abort_unless(AuthDriver::isOauth(), 404);

        try {
            $azureUser = Socialite::driver('azure')->user();
        } catch (Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['oauth' => 'Microsoft sign-in failed. Please try again.']);
        }

        if (! $this->emailDomainAllowed($azureUser->getEmail())) {
            return redirect()->route('login')->withErrors([
                'oauth' => 'Only '.config('portal.allowed_domain').' accounts may sign in.',
            ]);
        }

        $user = $provisioner->fromSocialite($azureUser);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /** Reject anyone whose email is outside the allowed company domain. */
    private function emailDomainAllowed(?string $email): bool
    {
        $allowed = config('portal.allowed_domain');

        if (blank($allowed)) {
            return false; // fail closed: no domain configured → nobody allowed
        }

        return $email !== null && Str::endsWith(Str::lower($email), '@'.Str::lower($allowed));
    }
}
