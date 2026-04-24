<x-admin.layouts.admin title="Mensajes de contacto"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Mensajes' => null,
    ]">
    <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="flex gap-2 items-center mb-4">
        <label class="text-sm text-slate-600">Filtro:</label>
        <select name="read" onchange="this.form.submit()" class="border-slate-300 rounded text-sm">
            <option value="">Todos</option>
            <option value="0" @selected($read === '0')>No leídos</option>
            <option value="1" @selected($read === '1')>Leídos</option>
        </select>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Asunto</th>
                    <th class="text-left p-3">Leído</th>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($messages as $m)
                    <tr class="{{ $m->read ? '' : 'font-semibold' }}">
                        <td class="p-3">
                            <a href="{{ route('admin.contact-messages.show', $m) }}" class="hover:underline">
                                {{ $m->name }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $m->email }}</td>
                        <td class="p-3 text-slate-600">{{ \Illuminate\Support\Str::limit($m->subject ?? '—', 50) }}</td>
                        <td class="p-3 text-slate-600">{{ $m->read ? 'Sí' : 'No' }}</td>
                        <td class="p-3 text-slate-600">{{ $m->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $m)
                                <form method="POST" action="{{ route('admin.contact-messages.destroy', $m) }}"
                                      onsubmit="return confirm('¿Eliminar mensaje?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin mensajes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $messages->links() }}</div>
</x-admin.layouts.admin>
