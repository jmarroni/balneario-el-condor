<x-admin.layouts.admin title="Páginas"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Páginas' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\Page::class)
            <a href="{{ route('admin.pages.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva página
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Slug</th>
                    <th class="text-left p-3">Publicada</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pages as $page)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.pages.edit', $page) }}" class="hover:underline">
                                {{ $page->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $page->slug }}</td>
                        <td class="p-3 text-slate-600">{{ $page->published ? 'Sí' : 'No' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $page)
                                <form method="POST" action="{{ route('admin.pages.destroy', $page) }}"
                                      onsubmit="return confirm('¿Eliminar página?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin páginas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pages->links() }}</div>
</x-admin.layouts.admin>
