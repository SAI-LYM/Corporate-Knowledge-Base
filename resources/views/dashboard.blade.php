@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php($user = auth()->user())

    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">
                    Welcome, {{ $user->name }}
                </h1>
                @if ($user->name_th)
                    <p class="text-gray-500">ยินดีต้อนรับ {{ $user->name_th }}</p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-mon-primary/10 px-3 py-1 text-sm font-medium text-mon-primary">
                    {{ $user->role?->name ?? 'Fresher' }}
                </span>
                <span class="inline-flex items-center rounded-full bg-mon-surface px-3 py-1 text-sm text-gray-600">
                    {{ $user->department?->name ?? 'No department' }}
                </span>
            </div>
        </div>

        <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-4">
                <dt class="text-gray-500">Email</dt>
                <dd class="mt-1 font-medium text-gray-800">{{ $user->email }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-4">
                <dt class="text-gray-500">Role / Department</dt>
                <dd class="mt-1 font-medium text-gray-800">
                    {{ $user->role?->label ?? '—' }} · {{ $user->department?->name ?? '—' }}
                </dd>
            </div>
        </dl>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <a href="{{ route('knowledge.index') }}"
                class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                <h2 class="font-semibold text-mon-primary">Knowledge Base</h2>
                <p class="text-sm text-gray-500">ฐานความรู้</p>
                <p class="mt-2 text-sm text-gray-600">Search articles filtered to your role &amp; department.</p>
            </a>
            <a href="{{ route('onboarding.index') }}"
                class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                <h2 class="font-semibold text-mon-primary">Onboarding Hub</h2>
                <p class="text-sm text-gray-500">คู่มือพนักงานใหม่</p>
                <p class="mt-2 text-sm text-gray-600">Your first-week checklist &amp; progress.</p>
            </a>
            @if ($user->isSenior())
                <a href="{{ route('projects.index') }}"
                    class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                    <h2 class="font-semibold text-mon-primary">Project Registry</h2>
                    <p class="text-sm text-gray-500">ทะเบียนโปรเจกต์</p>
                    <p class="mt-2 text-sm text-gray-600">Internal repos, tech specs &amp; READMEs (Senior+).</p>
                </a>
            @else
                <div class="rounded-lg border border-dashed border-gray-200 p-5 text-gray-400">
                    <h2 class="font-semibold">Project Registry</h2>
                    <p class="text-sm">Senior+ only</p>
                </div>
            @endif
        </div>

        @if ($user->isAdmin())
            <div class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-mon-primary/30 bg-mon-tint px-5 py-3 text-sm font-medium text-mon-primary transition hover:bg-mon-tint-hover">
                    ⚙ Admin panel
                    <span class="text-xs font-normal text-gray-500">manage users &amp; content</span>
                </a>
            </div>
        @endif
    </div>
@endsection
