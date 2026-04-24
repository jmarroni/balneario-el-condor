<x-admin.layouts.admin title="Lugares cercanos"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Lugares cercanos' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\NearbyPlace::class)
            <a href="{{ route('admin.nearby-places.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo lugar
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Dirección</th>
                    <th class="text-right p-3">Vistas</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($places as $place)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.nearby-places.edit', $place) }}" class="hover:underline">
                                {{ $place->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $place->address ?? '—' }}</td>
                        <td class="p-3 text-right text-slate-600">{{ $place->views }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $place)
                                <form method="POST" action="{{ route('admin.nearby-places.destroy', $place) }}"
                                      onsubmit="return confirm('¿Eliminar lugar cercano?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin lugares cercanos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $places->links() }}</div>
</x-admin.layouts.admin>
