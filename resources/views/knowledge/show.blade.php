@extends('layouts.app')

@section('title', $article->title)

@section('content')
    <article class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-10">
        <a href="{{ route('knowledge.index') }}" class="text-sm text-mon-primary hover:underline">← Knowledge Base</a>

        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
            <span class="rounded-full bg-mon-primary/10 px-2 py-0.5 font-medium text-mon-primary">{{ $article->department->name }}</span>
            <span class="rounded-full bg-mon-surface px-2 py-0.5 text-gray-600">{{ $article->category->name }}</span>
            <span class="rounded-full bg-mon-surface px-2 py-0.5 capitalize text-gray-500">{{ $article->audience_level }}</span>
        </div>

        <h1 class="mt-3 text-2xl font-semibold text-mon-primary md:text-3xl">{{ $article->title }}</h1>

        <p class="mt-2 text-sm text-gray-500">
            By {{ $article->author->name }}
            @if ($article->author->name_th)<span class="text-gray-400">({{ $article->author->name_th }})</span>@endif
            · updated {{ $article->updated_at->format('d M Y') }}
            · {{ $article->view_count }} views
        </p>

        {{-- Tags --}}
        @if ($article->tags->isNotEmpty())
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($article->tags as $tag)
                    <a href="{{ route('knowledge.index', ['tag' => $tag->slug]) }}"
                        class="rounded-full bg-mon-tint px-2.5 py-0.5 text-xs text-mon-primary hover:bg-mon-tint-hover">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <hr class="my-6 border-gray-100">

        {{-- Rendered Markdown: sanitized by MarkdownRenderer (CommonMark strip-HTML + Purifier). --}}
        <div class="markdown">
            {!! $html !!}
        </div>
    </article>
@endsection
