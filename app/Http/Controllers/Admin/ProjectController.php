<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Department;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

/**
 * Admin CRUD for Projects (CLAUDE.md §1 Admin module). Mirrors ArticleController:
 * gated by ProjectPolicy, validated by ProjectRequest, slug kept unique. Owners
 * are chosen from Senior+ users (the people allowed to own a project).
 */
class ProjectController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Project::class);

        return view('admin.projects.index', [
            'projects' => Project::with(['department', 'owner'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Project::class);

        return view('admin.projects.form', $this->formData(new Project));
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);

        $project = Project::create($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('status', "Project “{$project->name}” created.");
    }

    public function edit(Project $project): View
    {
        $this->authorize('update', $project);

        return view('admin.projects.form', $this->formData($project));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name'], $project->id);

        $project->update($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('status', "Project “{$project->name}” updated.");
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $name = $project->name;
        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('status', "Project “{$name}” deleted.");
    }

    /** Shared select options + current model for the create/edit form. */
    private function formData(Project $project): array
    {
        return [
            'project' => $project,
            'departments' => Department::orderBy('name')->get(),
            'owners' => User::whereHas('role', fn ($q) => $q->where('rank', '>=', Role::RANK_SENIOR))
                ->orderBy('name')
                ->get(),
        ];
    }

    /** Slugify the name, keeping it unique (ignoring the row being updated). */
    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'project';
        $slug = $base;
        $n = 2;

        while (Project::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
