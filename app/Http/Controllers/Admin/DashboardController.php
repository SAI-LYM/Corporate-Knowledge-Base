<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;

/**
 * Admin landing page (CLAUDE.md §1 — Admin module). Whole area is role:Admin
 * gated in routes/web.php; this just shows at-a-glance counts and entry points.
 */
class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'articleCount' => Article::count(),
            'projectCount' => Project::count(),
            'departmentCount' => Department::count(),
        ]);
    }
}
