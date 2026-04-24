<x-admin.layouts.admin title="Noticias"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Noticias' => null,
    ]">
    <div class="flex flex-wrap gap-2 items-center justify-between mb-4">
        <form method="GET" action="{{ route('admin.news.index') }}" class="flex gap-2 items-center">
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Buscar por título…"
                   class="border-slate-300 rounded text-sm px-3 py-2 w-64">
            <button type="submit"
                    class="bg-slate-200 text-slate-700 rounded px-3 py-2 text-sm hover:bg-slate-300">
                Buscar
            </button>
        </form>
        @can('create', App\Models\News::class)
            <a href="{{ route('admin.news.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva noticia
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Categoría</th>
                    <th class="text-left p-3">Publicación</th>
                    <th class="text-right p-3">Vistas</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($news as $item)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.news.edit', $item) }}" class="hover:underline">
                                {{ $item->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $item->category?->name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $item->published_at?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-3 text-right text-slate-600">{{ $item->views }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $item)
                                <form method="POST" action="{{ route('admin.news.destroy', $item) }}"
                                      onsubmit="return confirm('¿Eliminar noticia?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin noticias.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $news->links() }}</div>
</x-admin.layouts.admin>
