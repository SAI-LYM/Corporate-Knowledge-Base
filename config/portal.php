<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Driver
    |--------------------------------------------------------------------------
    |
    | Which login flow is active (CLAUDE.md §3 "Auth & Session"):
    |   - "mock"  : dev/demo only — pick a seeded user, no Azure. NEVER in prod.
    |   - "oauth" : Microsoft Entra ID (Azure AD) via Socialite.
    |
    | The mock path is additionally hard-blocked when APP_ENV=production (see
    | App\Support\AuthDriver::mockEnabled()), so flipping this in prod still fails.
    |
    */

    'auth_driver' => env('AUTH_DRIVER', 'mock'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Login Domain (OAuth)
    |--------------------------------------------------------------------------
    |
    | OAuth logins are restricted to this email domain, server-side. Anyone with
    | an email outside this domain is rejected (CLAUDE.md §3).
    |
    */

    'allowed_domain' => env('AUTH_ALLOWED_DOMAIN'),

];
