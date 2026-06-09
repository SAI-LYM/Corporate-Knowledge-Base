<?php

namespace App\Support;

use Illuminate\Support\Facades\App;

/**
 * Single source of truth for which login flow is active (CLAUDE.md §3).
 *
 * The mock driver is for local demos only and must be impossible to reach in
 * production — guarded here by BOTH the configured driver AND the environment,
 * so flipping AUTH_DRIVER=mock on a production box still refuses to log anyone in.
 */
class AuthDriver
{
    public static function current(): string
    {
        return (string) config('portal.auth_driver', 'mock');
    }

    public static function isOauth(): bool
    {
        return self::current() === 'oauth';
    }

    /** True only when the mock picker may be shown/used. Never in production. */
    public static function mockEnabled(): bool
    {
        return self::current() === 'mock' && ! App::environment('production');
    }
}
