<x-admin.layouts.admin title="Suscriptores al newsletter"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Suscriptores' => null,
    ]">
    <div class="flex flex-wrap gap-2 items-center justify-between mb-4">
        <form method="GET" action="{{ route('admin.newsletter-subscribers.index') }}" class="flex gap-2 items-center">
            <label class="text-sm text-slate-600">Estado:</label>
            <select name="status" onchange="this.form.submit()"
                    class="border-slate-300 rounded text-sm">
                <option value="">Todos</option>
                @foreach(['pending' => 'Pendientes', 'confirmed' => 'Confirmados', 'unsubscribed' => 'Baja'] as $k => $label)
                    <option value="{{ $k }}" @selected($status === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.newsletter-subscribers.export') }}"
           class="bg-slate-800 text-white rounded px-4 py-2 text-sm hover:bg-slate-700">
            Exportar CSV
        </a>
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Estado</th>
                    <th class="text-left p-3">Suscripción</th>
                    <th class="text-left p-3">Confirmación</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($subscribers as $sub)
                    <tr>
                        <td class="p-3">{{ $sub->email }}</td>
                        <td class="p-3 text-slate-600">{{ $sub->status }}</td>
                        <td class="p-3 text-slate-600">{{ $sub->subscribed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $sub->confirmed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $sub)
                                <form method="POST" action="{{ route('admin.newsletter-subscribers.destroy', $sub) }}"
                                      onsubmit="return confirm('¿Eliminar suscriptor?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin suscriptores.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $subscribers->links() }}</div>
</x-admin.layouts.admin>
