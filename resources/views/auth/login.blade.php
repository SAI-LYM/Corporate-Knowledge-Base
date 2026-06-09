@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-mon-card bg-white shadow-mon-card p-6 md:p-8">
            <h1 class="text-xl font-semibold text-mon-primary">Sign in</h1>
            <p class="mt-1 text-sm text-gray-500">เข้าสู่ระบบ MON Knowledge Hub</p>

            @if ($errors->any())
                <div class="mt-4 rounded-md border border-mon-danger/30 bg-mon-danger/10 px-4 py-3 text-sm text-mon-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Microsoft Entra ID (production / oauth driver) --}}
            @if ($oauthEnabled)
                <a href="{{ route('auth.azure.redirect') }}"
                    class="mt-6 flex w-full items-center justify-center gap-2 rounded-md bg-mon-primary px-4 py-2.5 font-medium text-white transition hover:bg-mon-primary-hover">
                    Sign in with Microsoft
                </a>
                <p class="mt-3 text-center text-xs text-gray-400">
                    Restricted to your company account.
                </p>
            @endif

            {{-- Mock login (local demo only — never reachable in production) --}}
            @if ($mockEnabled)
                <form method="POST" action="{{ route('login.mock') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">
                            Demo user <span class="text-gray-400">(พนักงานตัวอย่าง)</span>
                        </label>
                        <select id="user_id" name="user_id" required
                            class="mt-1 w-full rounded-md border-2 border-gray-200 px-3 py-2 text-sm focus:border-mon-primary focus:outline-none">
                            <option value="" disabled selected>— choose a user —</option>
                            @foreach ($mockUsers as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}@if ($user->name_th) ({{ $user->name_th }})@endif
                                    — {{ $user->role?->name ?? 'Fresher' }}, {{ $user->department?->name ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full rounded-md bg-mon-primary px-4 py-2.5 font-medium text-white transition hover:bg-mon-primary-hover">
                        Sign in (demo)
                    </button>
                </form>

                <p class="mt-4 rounded-md bg-mon-tint px-3 py-2 text-xs text-gray-500">
                    Demo mode — pick any seeded user to explore their role &amp; department.
                    Disabled automatically in production.
                </p>
            @endif

            @unless ($oauthEnabled || $mockEnabled)
                <p class="mt-6 text-sm text-mon-danger">
                    No login method is configured. Set <code>AUTH_DRIVER</code> to
                    <code>mock</code> (local) or <code>oauth</code>.
                </p>
            @endunless
        </div>
    </div>
@endsection
