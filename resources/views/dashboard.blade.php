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

        <p class="mt-6 text-gray-600">
            The Knowledge Base, Onboarding Hub, and Project Registry will appear here as
            they are built — each filtered to what your role and department may see.
        </p>
    </div>
@endsection
