<x-admin.layouts.admin title="Información útil"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Información útil' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\UsefulInfo::class)
            <a href="{{ route('admin.useful-info.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva entrada
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Orden</th>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                    <tr>
                        <td class="p-3 text-slate-600">{{ $item->sort_order }}</td>
                        <td class="p-3">
                            <a href="{{ route('admin.useful-info.edit', $item) }}" class="hover:underline">
                                {{ $item->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $item->phone ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $item->email ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $item)
                                <form method="POST" action="{{ route('admin.useful-info.destroy', $item) }}"
                                      onsubmit="return confirm('¿Eliminar entrada?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin entradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
</x-admin.layouts.admin>
