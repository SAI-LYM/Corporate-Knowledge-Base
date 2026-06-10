@extends('layouts.app')

@section('title', $project->exists ? 'Edit Project' : 'New Project')

@php
    $action = $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store');
    $roleOptions = [\App\Models\Role::RANK_FRESHER => 'Fresher', \App\Models\Role::RANK_SENIOR => 'Senior', \App\Models\Role::RANK_ADMIN => 'Admin'];
@endphp

@section('content')
    <div class="mx-auto max-w-3xl rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h1 class="text-2xl font-semibold text-mon-primary">{{ $project->exists ? 'Edit Project' : 'New Project' }}</h1>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-mon-primary hover:underline">← Projects</a>
        </div>

        @include('admin.partials.status')

        <form method="POST" action="{{ $action }}" class="mt-6 space-y-5">
            @csrf
            @if ($project->exists)
                @method('PUT')
            @endif

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name <span class="text-mon-danger">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}"
                    class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="repo_url" class="block text-sm font-medium text-gray-700">Repository URL <span class="text-mon-danger">*</span></label>
                    <input type="url" name="repo_url" id="repo_url" value="{{ old('repo_url', $project->repo_url) }}" placeholder="https://…"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                </div>
                <div>
                    <label for="tech_stack" class="block text-sm font-medium text-gray-700">Tech stack <span class="text-mon-danger">*</span></label>
                    <input type="text" name="tech_stack" id="tech_stack" value="{{ old('tech_stack', $project->tech_stack) }}" placeholder="PHP, REST, SQL"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                </div>
                <div>
                    <label for="owner_id" class="block text-sm font-medium text-gray-700">Owner (Senior+) <span class="text-mon-danger">*</span></label>
                    <select name="owner_id" id="owner_id"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}" @selected(old('owner_id', $project->owner_id) == $owner->id)>{{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-mon-danger">*</span></label>
                    <input type="text" name="status" id="status" value="{{ old('status', $project->status ?? 'Active') }}"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                </div>
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-mon-danger">*</span></label>
                    <select name="department_id" id="department_id"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $project->department_id) == $department->id)>
                                {{ $department->name }}{{ $department->is_global ? ' (global)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="audience_level" class="block text-sm font-medium text-gray-700">Audience level <span class="text-mon-danger">*</span></label>
                    <select name="audience_level" id="audience_level"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach (\App\Models\Article::AUDIENCE_LEVELS as $level)
                            <option value="{{ $level }}" @selected(old('audience_level', $project->audience_level ?? 'advanced') === $level)>{{ ucfirst($level) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="min_role" class="block text-sm font-medium text-gray-700">Minimum role <span class="text-mon-danger">*</span></label>
                    <select name="min_role" id="min_role"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach ($roleOptions as $rank => $label)
                            <option value="{{ $rank }}" @selected((int) old('min_role', $project->min_role ?? 2) === $rank)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label for="readme_markdown" class="block text-sm font-medium text-gray-700">README (Markdown) <span class="text-mon-danger">*</span></label>
                <textarea name="readme_markdown" id="readme_markdown" rows="12"
                    class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 font-mono text-sm focus:border-mon-primary focus:outline-none">{{ old('readme_markdown', $project->readme_markdown) }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Rendered XSS-safe on display (raw HTML is stripped).</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-mon-primary px-5 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                    {{ $project->exists ? 'Save changes' : 'Create project' }}
                </button>
                <a href="{{ route('admin.projects.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
