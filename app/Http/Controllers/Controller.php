<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    // Gives every controller $this->authorize() so write actions can be gated by
    // a Policy (CLAUDE.md §3 — RBAC enforced on the server, never inline ifs).
    use AuthorizesRequests;
}
