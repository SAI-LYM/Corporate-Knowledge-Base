@extends('layouts.app')

@section('title', 'Admin')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Admin</h1>
                <p class="text-sm text-gray-500">แผงผู้ดูแลระบบ · Manage users &amp; content</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-mon-primary hover:underline">← Dashboard</a>
        </div>

        @include('admin.partials.status')

        <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-4">
                <dt class="text-sm text-gray-500">Users</dt>
                <dd class="mt-1 text-2xl font-semibold text-mon-primary">{{ $userCount }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-4">
                <dt class="text-sm text-gray-500">Articles</dt>
                <dd class="mt-1 text-2xl font-semibold text-mon-primary">{{ $articleCount }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-4">
                <dt class="text-sm text-gray-500">Projects</dt>
                <dd class="mt-1 text-2xl font-semibold text-mon-primary">{{ $projectCount }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-4">
                <dt class="text-sm text-gray-500">Departments</dt>
                <dd class="mt-1 text-2xl font-semibold text-mon-primary">{{ $departmentCount }}</dd>
            </div>
        </dl>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <a href="{{ route('admin.users.index') }}"
                class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                <h2 class="font-semibold text-mon-primary">Manage Users</h2>
                <p class="mt-2 text-sm text-gray-600">Assign each user a role &amp; department.</p>
            </a>
            <a href="{{ route('admin.articles.index') }}"
                class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                <h2 class="font-semibold text-mon-primary">Manage Articles</h2>
                <p class="mt-2 text-sm text-gray-600">Create, edit &amp; delete Knowledge Base articles.</p>
            </a>
            <a href="{{ route('admin.projects.index') }}"
                class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                <h2 class="font-semibold text-mon-primary">Manage Projects</h2>
                <p class="mt-2 text-sm text-gray-600">Create, edit &amp; delete registered projects.</p>
            </a>
        </div>
    </div>
@endsection
