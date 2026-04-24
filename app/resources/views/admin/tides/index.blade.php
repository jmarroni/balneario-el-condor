<x-admin.layouts.admin title="Mareas"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Mareas' => null,
    ]">
    <div class="flex justify-end gap-2 mb-4">
        @can('create', App\Models\Tide::class)
            <a href="{{ route('admin.tides.import.form') }}"
               class="bg-slate-200 text-slate-800 rounded px-4 py-2 hover:bg-slate-300">
                Importar CSV
            </a>
            <a href="{{ route('admin.tides.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva marea
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-left p-3">Ubicación</th>
                    <th class="text-left p-3">1ª alta</th>
                    <th class="text-left p-3">1ª baja</th>
                    <th class="text-left p-3">2ª alta</th>
                    <th class="text-left p-3">2ª baja</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($tides as $tide)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.tides.edit', $tide) }}" class="hover:underline">
                                {{ $tide->date?->format('d/m/Y') }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $tide->location }}</td>
                        <td class="p-3 text-slate-600">{{ $tide->first_high ?? '—' }} {{ $tide->first_high_height }}</td>
                        <td class="p-3 text-slate-600">{{ $tide->first_low ?? '—' }} {{ $tide->first_low_height }}</td>
                        <td class="p-3 text-slate-600">{{ $tide->second_high ?? '—' }} {{ $tide->second_high_height }}</td>
                        <td class="p-3 text-slate-600">{{ $tide->second_low ?? '—' }} {{ $tide->second_low_height }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $tide)
                                <form method="POST" action="{{ route('admin.tides.destroy', $tide) }}"
                                      onsubmit="return confirm('¿Eliminar marea?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-6 text-center text-slate-500">Sin mareas cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tides->links() }}</div>
</x-admin.layouts.admin>
