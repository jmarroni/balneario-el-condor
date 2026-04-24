<x-admin.layouts.admin title="Inscripciones: {{ $event->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Eventos' => route('admin.events.index'),
        $event->title => route('admin.events.edit', $event),
        'Inscripciones' => null,
    ]">
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-left p-3">Ciudad</th>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($registrations as $r)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.registrations.show', $r) }}" class="hover:underline">
                                {{ trim($r->name . ' ' . ($r->last_name ?? '')) }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $r->email ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $r->phone ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $r->city ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $r->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $r)
                                <form method="POST" action="{{ route('admin.registrations.destroy', $r) }}"
                                      onsubmit="return confirm('¿Eliminar inscripción?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin inscripciones.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $registrations->links() }}</div>
</x-admin.layouts.admin>
