@extends('layouts.app')

@section('title', 'Manage Projects')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Manage Projects</h1>
                <p class="text-sm text-gray-500">จัดการโปรเจกต์ · {{ $projects->total() }} project(s)</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-mon-primary hover:underline">← Admin</a>
                <a href="{{ route('admin.projects.create') }}"
                    class="rounded-md bg-mon-primary px-4 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                    + New project
                </a>
            </div>
        </div>

        @include('admin.partials.status')

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="py-2 pr-4 font-medium">Name</th>
                        <th class="py-2 pr-4 font-medium">Department</th>
                        <th class="py-2 pr-4 font-medium">Owner</th>
                        <th class="py-2 pr-4 font-medium">Status</th>
                        <th class="py-2 pr-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr class="border-b border-gray-100 transition hover:bg-mon-tint">
                            <td class="py-2 pr-4 font-medium text-gray-800">{{ $project->name }}</td>
                            <td class="py-2 pr-4 text-gray-600">{{ $project->department?->name ?? '—' }}</td>
                            <td class="py-2 pr-4 text-gray-600">{{ $project->owner?->name ?? '—' }}</td>
                            <td class="py-2 pr-4">
                                <span class="rounded-full bg-mon-success/10 px-2 py-0.5 text-xs font-medium text-mon-success">{{ $project->status }}</span>
                            </td>
                            <td class="py-2 pr-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="font-medium text-mon-primary hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                        onsubmit="return confirm('Delete “{{ $project->name }}”? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-mon-danger hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">No projects yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $projects->links() }}</div>
    </div>
@endsection
