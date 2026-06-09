@extends('layouts.app')

@section('title', 'Onboarding Hub')

@section('content')
    <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-mon-primary">Onboarding Hub</h1>
                <p class="text-sm text-gray-500">รายการสิ่งที่ต้องทำสำหรับพนักงานใหม่ · your first-week checklist</p>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-mon-primary hover:underline">← Dashboard</a>
        </div>

        {{-- Progress bar --}}
        <div class="mt-6">
            <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-gray-700">Progress</span>
                <span class="text-gray-500">{{ $done }} / {{ $total }} · {{ $percent }}%</span>
            </div>
            <div class="mt-2 h-3 w-full overflow-hidden rounded-full bg-mon-surface">
                <div class="h-full rounded-full bg-mon-success transition-all" style="width: {{ $percent }}%"></div>
            </div>
            @if ($percent === 100)
                <p class="mt-2 text-sm font-medium text-mon-success">🎉 All done — welcome aboard!</p>
            @endif
        </div>

        {{-- Checklist --}}
        <ul class="mt-6 divide-y divide-gray-100">
            @forelse ($items as $item)
                @php($checked = in_array($item->id, $completedIds, true))
                <li class="flex items-start gap-3 py-3">
                    <form method="POST" action="{{ route('onboarding.toggle', $item) }}" class="pt-0.5">
                        @csrf
                        <input type="checkbox"
                            onchange="this.form.submit()"
                            @checked($checked)
                            class="h-5 w-5 cursor-pointer rounded border-gray-300 text-mon-primary focus:ring-mon-primary"
                            aria-label="Toggle: {{ $item->title }}">
                    </form>
                    <div class="min-w-0">
                        <p class="font-medium {{ $checked ? 'text-gray-400 line-through' : 'text-gray-800' }}">
                            {{ $item->title }}
                        </p>
                        @if ($item->description)
                            <p class="text-sm text-gray-500">{{ $item->description }}</p>
                        @endif
                        @if ($item->department_id)
                            <span class="mt-1 inline-block rounded-full bg-mon-tint px-2 py-0.5 text-xs text-mon-primary">
                                {{ $item->department->name }}
                            </span>
                        @endif
                    </div>
                </li>
            @empty
                <li class="py-6 text-center text-gray-500">No checklist items for your department yet.</li>
            @endforelse
        </ul>
    </div>
@endsection
