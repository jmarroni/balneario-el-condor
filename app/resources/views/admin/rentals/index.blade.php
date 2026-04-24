<x-admin.layouts.admin title="Alquileres"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Alquileres' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\Rental::class)
            <a href="{{ route('admin.rentals.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo alquiler
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Plazas</th>
                    <th class="text-left p-3">Contacto</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rentals as $rental)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.rentals.edit', $rental) }}" class="hover:underline">
                                {{ $rental->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $rental->places ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $rental->contact_name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $rental->phone ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $rental)
                                <form method="POST" action="{{ route('admin.rentals.destroy', $rental) }}"
                                      onsubmit="return confirm('¿Eliminar alquiler?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin alquileres.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rentals->links() }}</div>
</x-admin.layouts.admin>
