@extends('layouts.app')

@section('title', 'Project Registry')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Project Registry</h1>
                <p class="text-sm text-gray-500">ทะเบียนโปรเจกต์ · {{ $projects->total() }} project(s) you can see</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-mon-primary hover:underline">← Dashboard</a>
        </div>

        <form method="GET" action="{{ route('projects.index') }}" class="mt-6 flex gap-2">
            <input type="search" name="q" value="{{ $q }}" placeholder="Search name or tech stack…"
                class="w-full max-w-sm rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
            <button type="submit" class="rounded-md bg-mon-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                Search
            </button>
            @if ($q)
                <a href="{{ route('projects.index') }}" class="rounded-md border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-mon-surface">Clear</a>
            @endif
        </form>
    </div>

    @if ($projects->isEmpty())
        <div class="mt-6 rounded-mon-card bg-white shadow-mon-card p-8 text-center text-gray-500">
            No projects are visible to your role &amp; department.
        </div>
    @else
        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                <div class="flex flex-col rounded-mon-card bg-white shadow-mon-card p-5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="rounded-full bg-mon-primary/10 px-2 py-0.5 text-xs font-medium text-mon-primary">{{ $project->department->name }}</span>
                        <span class="rounded-full bg-mon-success/10 px-2 py-0.5 text-xs font-medium text-mon-success">{{ $project->status }}</span>
                    </div>

                    <a href="{{ route('projects.show', $project->slug) }}" class="mt-3 font-semibold text-gray-800 hover:text-mon-primary">
                        {{ $project->name }}
                    </a>
                    <p class="mt-1 text-sm text-gray-500">{{ $project->tech_stack }}</p>

                    <div class="mt-auto pt-4 text-xs text-gray-400">
                        Owner: {{ $project->owner->name }}
                    </div>
                    <div class="mt-3 flex items-center gap-3 text-sm">
                        <a href="{{ route('projects.show', $project->slug) }}" class="font-medium text-mon-primary hover:underline">View README →</a>
                        <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:underline">Repo ↗</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $projects->links() }}</div>
    @endif
@endsection
