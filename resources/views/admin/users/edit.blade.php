@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <div class="mx-auto max-w-xl rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h1 class="text-2xl font-semibold text-mon-primary">Edit User</h1>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-mon-primary hover:underline">← Users</a>
        </div>

        @include('admin.partials.status')

        <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-3">
                <dt class="text-gray-500">Name</dt>
                <dd class="font-medium text-gray-800">{{ $user->name }}</dd>
            </div>
            <div class="rounded-lg border border-gray-200 bg-mon-surface p-3">
                <dt class="text-gray-500">Email</dt>
                <dd class="font-medium text-gray-800">{{ $user->email }}</dd>
            </div>
        </dl>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="role_id" class="block text-sm font-medium text-gray-700">Role <span class="text-mon-danger">*</span></label>
                <select name="role_id" id="role_id"
                    class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                            {{ $role->label }} ({{ $role->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">Department <span class="text-mon-danger">*</span></label>
                <select name="department_id" id="department_id"
                    class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id', $user->department_id) == $department->id)>
                            {{ $department->name }}{{ $department->is_global ? ' (global)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-md bg-mon-primary px-5 py-2 text-sm font-medium text-white transition hover:bg-mon-primary-hover">
                    Save changes
                </button>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
@endsection
