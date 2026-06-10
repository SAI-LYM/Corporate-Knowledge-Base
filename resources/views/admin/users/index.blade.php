@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Manage Users</h1>
                <p class="text-sm text-gray-500">จัดการผู้ใช้ · {{ $users->total() }} user(s)</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-mon-primary hover:underline">← Admin</a>
        </div>

        @include('admin.partials.status')

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500">
                        <th class="py-2 pr-4 font-medium">Name</th>
                        <th class="py-2 pr-4 font-medium">Email</th>
                        <th class="py-2 pr-4 font-medium">Role</th>
                        <th class="py-2 pr-4 font-medium">Department</th>
                        <th class="py-2 pr-4 font-medium"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-100 transition hover:bg-mon-tint">
                            <td class="py-2 pr-4 font-medium text-gray-800">
                                {{ $user->name }}
                                @if ($user->name_th)
                                    <span class="block text-xs text-gray-400">{{ $user->name_th }}</span>
                                @endif
                            </td>
                            <td class="py-2 pr-4 text-gray-600">{{ $user->email }}</td>
                            <td class="py-2 pr-4">
                                <span class="rounded-full bg-mon-primary/10 px-2 py-0.5 text-xs font-medium text-mon-primary">
                                    {{ $user->role?->name ?? 'Fresher' }}
                                </span>
                            </td>
                            <td class="py-2 pr-4 text-gray-600">{{ $user->department?->name ?? '—' }}</td>
                            <td class="py-2 pr-4 text-right">
                                <a href="{{ route('admin.users.edit', $user) }}" class="font-medium text-mon-primary hover:underline">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    </div>
@endsection
