<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\MarkdownRenderer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Project Registry (CLAUDE.md §1, BUILD_GUIDE Part A). Projects default to
 * advanced / min-role Senior, so Freshers see none. Reads go through
 * Project::visibleTo($user) (same RBAC as Articles); the detail page uses the
 * scope so a hidden project 404s instead of revealing its existence.
 */
class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::visibleTo($request->user())
            ->with(['owner', 'department'])
            ->when($request->query('q'), fn ($query, $term) =>
                $query->where(fn ($q) => $q
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('tech_stack', 'like', "%{$term}%")))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'q' => $request->query('q'),
        ]);
    }

    public function show(Request $request, string $slug, MarkdownRenderer $markdown): View
    {
        $project = Project::visibleTo($request->user())
            ->with(['owner', 'department'])
            ->where('slug', $slug)
            ->firstOrFail();

        $project->increment('view_count');

        return view('projects.show', [
            'project' => $project,
            'html' => $markdown->toSafeHtml($project->readme_markdown),
        ]);
    }
}
