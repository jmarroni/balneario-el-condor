<x-admin.layouts.admin title="Alojamientos"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Alojamientos' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\Lodging::class)
            <a href="{{ route('admin.lodgings.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo alojamiento
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Tipo</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-right p-3">Vistas</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($lodgings as $lodging)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.lodgings.edit', $lodging) }}" class="hover:underline">
                                {{ $lodging->name }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $lodging->type }}</td>
                        <td class="p-3 text-slate-600">{{ $lodging->phone ?? '—' }}</td>
                        <td class="p-3 text-right text-slate-600">{{ $lodging->views }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $lodging)
                                <form method="POST" action="{{ route('admin.lodgings.destroy', $lodging) }}"
                                      onsubmit="return confirm('¿Eliminar alojamiento?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin alojamientos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $lodgings->links() }}</div>
</x-admin.layouts.admin>
