<x-admin.layouts.admin title="Dashboard">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($stats as $label => $count)
            <div class="bg-white rounded shadow p-4">
                <p class="text-xs uppercase text-slate-500">{{ $label }}</p>
                <p class="text-2xl font-bold mt-1">{{ $count }}</p>
            </div>
        @endforeach
    </div>
</x-admin.layouts.admin>
