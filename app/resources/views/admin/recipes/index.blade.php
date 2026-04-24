<x-admin.layouts.admin title="Recetas"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Recetas' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\Recipe::class)
            <a href="{{ route('admin.recipes.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva receta
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Autor</th>
                    <th class="text-left p-3">Publicada</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recipes as $recipe)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.recipes.edit', $recipe) }}" class="hover:underline">
                                {{ $recipe->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $recipe->author ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $recipe->published_on?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $recipe)
                                <form method="POST" action="{{ route('admin.recipes.destroy', $recipe) }}"
                                      onsubmit="return confirm('¿Eliminar receta?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin recetas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $recipes->links() }}</div>
</x-admin.layouts.admin>
