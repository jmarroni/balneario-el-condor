<x-admin.layouts.admin title="Locales gastronómicos y nocturnos"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Locales' => null,
    ]">
    <div class="flex justify-between items-center mb-4">
        <form method="GET" class="flex items-center gap-2">
            <label for="category" class="text-sm text-slate-600">Filtrar:</label>
            <select name="category" id="category" class="border-slate-300 rounded text-sm"
                    onchange="this.form.submit()">
                <option value="">Todas las categorías</option>
                <option value="gourmet" @selected(request('category') === 'gourmet')>Gourmet</option>
                <option value="nightlife" @selected(request('category') === 'nightlife')>Nocturnos</option>
            </select>
        </form>

        @can('create', App\Models\Venue::class)
            <a href="{{ route('admin.venues.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo local
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Categoría</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-right p-3">Vistas</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($venues as $venue)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.venues.edit', $venue) }}" class="hover:underline">
                                {{ $venue->name }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $venue->category }}</td>
                        <td class="p-3 text-slate-600">{{ $venue->phone ?? '—' }}</td>
                        <td class="p-3 text-right text-slate-600">{{ $venue->views }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $venue)
                                <form method="POST" action="{{ route('admin.venues.destroy', $venue) }}"
                                      onsubmit="return confirm('¿Eliminar local?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin locales.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $venues->links() }}</div>
</x-admin.layouts.admin>
