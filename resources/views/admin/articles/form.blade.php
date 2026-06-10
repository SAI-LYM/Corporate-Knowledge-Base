@extends('layouts.app')

@section('title', $article->exists ? 'Edit Article' : 'New Article')

@php
    $action = $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store');
    $roleOptions = [\App\Models\Role::RANK_FRESHER => 'Fresher', \App\Models\Role::RANK_SENIOR => 'Senior', \App\Models\Role::RANK_ADMIN => 'Admin'];
@endphp

@section('content')
    <div class="mx-auto max-w-3xl rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h1 class="text-2xl font-semibold text-mon-primary">{{ $article->exists ? 'Edit Article' : 'New Article' }}</h1>
            <a href="{{ route('admin.articles.index') }}" class="text-sm text-mon-primary hover:underline">← Articles</a>
        </div>

        @include('admin.partials.status')

        <form method="POST" action="{{ $action }}" class="mt-6 space-y-5">
            @csrf
            @if ($article->exists)
                @method('PUT')
            @endif

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-mon-danger">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}"
                    class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-mon-danger">*</span></label>
                    <select name="department_id" id="department_id"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $article->department_id) == $department->id)>
                                {{ $department->name }}{{ $department->is_global ? ' (global)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category <span class="text-mon-danger">*</span></label>
                    <select name="category_id" id="category_id"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $article->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="audience_level" class="block text-sm font-medium text-gray-700">Audience level <span class="text-mon-danger">*</span></label>
                    <select name="audience_level" id="audience_level"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach (\App\Models\Article::AUDIENCE_LEVELS as $level)
                            <option value="{{ $level }}" @selected(old('audience_level', $article->audience_level) === $level)>{{ ucfirst($level) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="min_role" class="block text-sm font-medium text-gray-700">Minimum role <span class="text-mon-danger">*</span></label>
                    <select name="min_role" id="min_role"
                        class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                        @foreach ($roleOptions as $rank => $label)
                            <option value="{{ $rank }}" @selected((int) old('min_role', $article->min_role ?? 1) === $rank)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <span class="block text-sm font-medium text-gray-700">Tags</span>
                <div class="mt-2 flex flex-wrap gap-3">
                    @forelse ($tags as $tag)
                        <label class="inline-flex items-center gap-1.5 text-sm text-gray-700">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                @checked(in_array($tag->id, old('tags', $selectedTags)))
                                class="rounded border-gray-300 text-mon-primary focus:ring-mon-primary">
                            {{ $tag->name }}
                        </label>
                    @empty
                        <span class="text-sm text-gray-400">No tags defined.</span>
                    @endforelse
                </div>
            </div>

            <div>
                <label for="body_markdown" class="block text-sm font-medium text-gray-700">Body (Markdown) <span class="text-mon-danger">*</span></label>
                <textarea name="body_markdown" id="body_markdown" rows="12"
                    class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 font-mono text-sm focus:border-mon-primary focus:outline-none">{{ old('body_markdown', $article->body_markdown) }}</textarea>
                <p class="mt-1 text-xs text-gray-400">Rendered XSS-safe on display (raw HTML is stripped).</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-mon-primary px-5 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                    {{ $article->exists ? 'Save changes' : 'Create article' }}
                </button>
                <a href="{{ route('admin.articles.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
