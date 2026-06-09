@extends('layouts.app')

@section('title', 'Knowledge Base')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Knowledge Base</h1>
                <p class="text-sm text-gray-500">ฐานความรู้ · {{ $articles->total() }} article(s) you can see</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-mon-primary hover:underline">← Dashboard</a>
        </div>

        {{-- Filter / search bar --}}
        <form method="GET" action="{{ route('knowledge.index') }}" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search…"
                class="rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none lg:col-span-2">

            <select name="department" class="rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                <option value="">All departments</option>
                @foreach ($departments as $d)
                    <option value="{{ $d->code }}" @selected(($filters['department'] ?? '') === $d->code)>{{ $d->name }}</option>
                @endforeach
            </select>

            <select name="category" class="rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                <option value="">All categories</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->slug }}" @selected(($filters['category'] ?? '') === $c->slug)>{{ $c->name }}</option>
                @endforeach
            </select>

            <select name="tag" class="rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                <option value="">All tags</option>
                @foreach ($tags as $t)
                    <option value="{{ $t->slug }}" @selected(($filters['tag'] ?? '') === $t->slug)>{{ $t->name }}</option>
                @endforeach
            </select>

            <div class="flex gap-2 sm:col-span-2 lg:col-span-5">
                <button type="submit" class="rounded-md bg-mon-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                    Apply
                </button>
                @if (array_filter($filters))
                    <a href="{{ route('knowledge.index') }}" class="rounded-md border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-mon-surface">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if ($articles->isEmpty())
        <div class="mt-6 rounded-mon-card bg-white shadow-mon-card p-8 text-center text-gray-500">
            No articles match your filters — or none are visible to your role &amp; department.
        </div>
    @else
        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($articles as $article)
                <a href="{{ route('knowledge.show', $article->slug) }}"
                    class="flex flex-col rounded-mon-card bg-white shadow-mon-card p-5 transition hover:bg-mon-tint">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-mon-primary/10 px-2 py-0.5 font-medium text-mon-primary">{{ $article->department->name }}</span>
                        <span class="rounded-full bg-mon-surface px-2 py-0.5 text-gray-600">{{ $article->category->name }}</span>
                        <span class="rounded-full bg-mon-surface px-2 py-0.5 capitalize text-gray-500">{{ $article->audience_level }}</span>
                    </div>
                    <h2 class="mt-3 font-semibold text-gray-800">{{ $article->title }}</h2>
                    <p class="mt-auto pt-3 text-xs text-gray-400">
                        {{ $article->author->name }} · updated {{ $article->updated_at->diffForHumans() }}
                    </p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
