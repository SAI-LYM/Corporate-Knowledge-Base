<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

/**
 * Turns a verified Microsoft Entra ID profile into a local User (CLAUDE.md §3).
 *
 * RBAC is independent of the login method: a brand-new OAuth user is created with
 * the default **Fresher** role and no department (an Admin assigns these later).
 * Existing users are matched by Azure object id first, then by email, and never
 * get a plain-text password — OAuth accounts have none.
 */
class AzureUserProvisioner
{
    public function fromSocialite(SocialiteUser $azureUser): User
    {
        $email = (string) $azureUser->getEmail();
        $oid = (string) $azureUser->getId();

        // Match an existing account by Azure oid, falling back to email.
        $user = User::where('azure_oid', $oid)->first()
            ?? User::where('email', $email)->first();

        if (! $user) {
            $user = new User;
            $user->role_id = Role::where('name', Role::FRESHER)->value('id'); // default on first login
        }

        $user->fill([
            'name' => $azureUser->getName() ?: $email,
            'email' => $email,
            'azure_oid' => $oid,
        ]);

        $user->save();

        return $user;
    }
}
