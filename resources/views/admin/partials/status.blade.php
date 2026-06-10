@if (session('status'))
    <div class="mt-4 rounded-lg border border-mon-success/30 bg-mon-success/10 px-4 py-3 text-sm text-mon-success">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mt-4 rounded-lg border border-mon-danger/30 bg-mon-danger/10 px-4 py-3 text-sm text-mon-danger">
        <p class="font-medium">Please fix the following:</p>
        <ul class="mt-1 list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
