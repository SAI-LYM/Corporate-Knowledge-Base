@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-10">
        <h1 class="text-2xl md:text-3xl font-semibold text-mon-primary">
            Welcome to the MON Knowledge Hub
        </h1>
        <p class="mt-1 text-lg text-gray-600">ยินดีต้อนรับสู่ศูนย์ความรู้ MON Logistics</p>

        <p class="mt-4 max-w-2xl text-gray-700 leading-relaxed">
            One place for onboarding, company knowledge, coding standards, and internal projects.
            This is a theme &amp; layout placeholder — the Onboarding Hub, Knowledge Base, and
            Project Registry come next.
        </p>

        {{-- Module preview cards --}}
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['Onboarding Hub', 'คู่มือพนักงานใหม่', 'Role-filtered first-week guides & checklists.'],
                ['Knowledge Base', 'ฐานความรู้', 'Searchable articles by department, category & tag.'],
                ['Project Registry', 'ทะเบียนโปรเจกต์', 'Internal repos, tech specs & READMEs.'],
            ] as [$en, $th, $desc])
                <div class="rounded-lg border border-gray-200 bg-mon-surface p-5 transition hover:bg-mon-tint">
                    <h2 class="font-semibold text-mon-primary">{{ $en }}</h2>
                    <p class="text-sm text-gray-500">{{ $th }}</p>
                    <p class="mt-2 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        {{-- Button sample (proves brand color + hover token) --}}
        <div class="mt-8 flex flex-wrap items-center gap-3">
            <button type="button"
                class="rounded-md bg-mon-primary px-5 py-2.5 font-medium text-white transition hover:bg-mon-primary-hover">
                Primary action
            </button>
            <span class="inline-flex items-center rounded-md bg-mon-success/10 px-3 py-1 text-sm font-medium text-mon-success">
                Theme loaded ✓
            </span>
        </div>
    </div>
@endsection
