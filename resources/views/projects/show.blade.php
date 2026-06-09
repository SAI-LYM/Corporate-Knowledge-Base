@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <article class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-10">
        <a href="{{ route('projects.index') }}" class="text-sm text-mon-primary hover:underline">← Project Registry</a>

        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
            <span class="rounded-full bg-mon-primary/10 px-2 py-0.5 font-medium text-mon-primary">{{ $project->department->name }}</span>
            <span class="rounded-full bg-mon-success/10 px-2 py-0.5 font-medium text-mon-success">{{ $project->status }}</span>
        </div>

        <h1 class="mt-3 text-2xl font-semibold text-mon-primary md:text-3xl">{{ $project->name }}</h1>

        {{-- Meta --}}
        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-3">
                <dt class="text-gray-500">Owner</dt>
                <dd class="font-medium text-gray-800">{{ $project->owner->name }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-3">
                <dt class="text-gray-500">Tech stack</dt>
                <dd class="font-medium text-gray-800">{{ $project->tech_stack }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-3">
                <dt class="text-gray-500">Repository</dt>
                <dd class="truncate font-medium">
                    <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="text-mon-primary hover:underline">
                        {{ $project->repo_url }} ↗
                    </a>
                </dd>
            </div>
        </dl>

        <hr class="my-6 border-gray-100">

        {{-- README: sanitized by MarkdownRenderer (CommonMark strip-HTML + Purifier). --}}
        <div class="markdown">
            {!! $html !!}
        </div>
    </article>
@endsection
