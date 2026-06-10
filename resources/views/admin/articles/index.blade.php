@extends('layouts.app')

@section('title', 'Manage Articles')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Manage Articles</h1>
                <p class="text-sm text-gray-500">จัดการบทความ · {{ $articles->total() }} article(s)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-mon-primary hover:underline">← Admin</a>
                <a href="{{ route('admin.articles.create') }}"
                    class="rounded-md bg-mon-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                    + New article
                </a>
            </div>
        </div>

        @include('admin.partials.status')

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="py-2 pr-4 font-medium">Title</th>
                        <th class="py-2 pr-4 font-medium">Department</th>
                        <th class="py-2 pr-4 font-medium">Audience</th>
                        <th class="py-2 pr-4 font-medium">Author</th>
                        <th class="py-2 pr-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $article)
                        <tr class="border-b border-gray-100 transition hover:bg-mon-tint">
                            <td class="py-2 pr-4 font-medium text-gray-800">{{ $article->title }}</td>
                            <td class="py-2 pr-4 text-gray-600">{{ $article->department?->name ?? '—' }}</td>
                            <td class="py-2 pr-4">
                                <span class="rounded-full bg-mon-primary/10 px-2 py-0.5 text-xs font-medium text-mon-primary">{{ $article->audience_level }}</span>
                            </td>
                            <td class="py-2 pr-4 text-gray-600">{{ $article->author?->name ?? '—' }}</td>
                            <td class="py-2 pr-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="font-medium text-mon-primary hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                                        onsubmit="return confirm('Delete “{{ $article->title }}”? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-mon-danger hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">No articles yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $articles->links() }}</div>
    </div>
@endsection
