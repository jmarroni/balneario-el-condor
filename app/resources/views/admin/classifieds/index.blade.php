<x-admin.layouts.admin title="Clasificados"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Clasificados' => null,
    ]">
    <div class="flex flex-wrap gap-2 items-center justify-between mb-4">
        <form method="GET" action="{{ route('admin.classifieds.index') }}" class="flex gap-2 items-center">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Buscar por título…"
                   class="border-slate-300 rounded text-sm px-3 py-2 w-64">
            <button type="submit"
                    class="bg-slate-200 text-slate-700 rounded px-3 py-2 text-sm hover:bg-slate-300">
                Buscar
            </button>
        </form>
        @can('create', App\Models\Classified::class)
            <a href="{{ route('admin.classifieds.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo clasificado
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Categoría</th>
                    <th class="text-left p-3">Contacto</th>
                    <th class="text-left p-3">Publicación</th>
                    <th class="text-right p-3">Vistas</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($classifieds as $classified)
                    <tr>
                        <td class="p-3">
                            @can('update', $classified)
                                <a href="{{ route('admin.classifieds.edit', $classified) }}" class="hover:underline">
                                    {{ $classified->title }}
                                </a>
                            @else
                                {{ $classified->title }}
                            @endcan
                        </td>
                        <td class="p-3 text-slate-600">{{ $classified->category?->name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $classified->contact_name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $classified->published_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-3 text-right text-slate-600">{{ $classified->views }}</td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.classifieds.contacts.index', $classified) }}"
                               class="text-slate-600 hover:underline">Contactos</a>
                            @can('delete', $classified)
                                <form method="POST" action="{{ route('admin.classifieds.destroy', $classified) }}"
                                      onsubmit="return confirm('¿Eliminar clasificado?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin clasificados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $classifieds->links() }}</div>
</x-admin.layouts.admin>
