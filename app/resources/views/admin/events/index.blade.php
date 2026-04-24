<x-admin.layouts.admin title="Eventos"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Eventos' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\Event::class)
            <a href="{{ route('admin.events.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo evento
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Lugar</th>
                    <th class="text-left p-3">Inicio</th>
                    <th class="text-left p-3">Destacado</th>
                    <th class="text-left p-3">Inscripciones</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($events as $event)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.events.edit', $event) }}" class="hover:underline">
                                {{ $event->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $event->location ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $event->starts_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $event->featured ? 'Sí' : 'No' }}</td>
                        <td class="p-3 text-slate-600">{{ $event->accepts_registrations ? 'Abiertas' : 'Cerradas' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $event)
                                <form method="POST" action="{{ route('admin.events.destroy', $event) }}"
                                      onsubmit="return confirm('¿Eliminar evento?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin eventos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $events->links() }}</div>
</x-admin.layouts.admin>
