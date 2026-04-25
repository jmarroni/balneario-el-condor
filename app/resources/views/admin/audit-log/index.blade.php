<x-admin.layouts.admin title="Bitácora de cambios"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Bitácora' => null,
    ]">
    <form method="GET" action="{{ route('admin.audit-log.index') }}"
          class="bg-white rounded shadow p-4 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <label class="text-sm">
            <span class="block text-slate-600 mb-1">Usuario</span>
            <select name="user_id" class="border-slate-300 rounded text-sm w-full px-2 py-1.5">
                <option value="">Todos</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected((string) $filters['user_id'] === (string) $u->id)>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                @endforeach
            </select>
        </label>

        <label class="text-sm">
            <span class="block text-slate-600 mb-1">Modelo</span>
            <input type="text" name="subject_type" value="{{ $filters['subject_type'] }}"
                   placeholder="ej. News"
                   class="border-slate-300 rounded text-sm w-full px-2 py-1.5">
        </label>

        <label class="text-sm">
            <span class="block text-slate-600 mb-1">Desde</span>
            <input type="date" name="from" value="{{ $filters['from'] }}"
                   class="border-slate-300 rounded text-sm w-full px-2 py-1.5">
        </label>

        <label class="text-sm">
            <span class="block text-slate-600 mb-1">Hasta</span>
            <input type="date" name="to" value="{{ $filters['to'] }}"
                   class="border-slate-300 rounded text-sm w-full px-2 py-1.5">
        </label>

        <div class="md:col-span-4 flex gap-2">
            <button type="submit"
                    class="bg-slate-800 text-white rounded px-4 py-2 text-sm hover:bg-slate-700">
                Filtrar
            </button>
            <a href="{{ route('admin.audit-log.index') }}"
               class="bg-slate-200 text-slate-700 rounded px-4 py-2 text-sm hover:bg-slate-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-left p-3">Usuario</th>
                    <th class="text-left p-3">Acción</th>
                    <th class="text-left p-3">Recurso</th>
                    <th class="text-left p-3">Cambios</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    @php
                        $causerName = $log->causer?->name ?? 'Sistema';
                        $subjectShort = $log->subject_type
                            ? class_basename($log->subject_type)
                            : '—';
                        $changes = $log->changes();
                    @endphp
                    <tr class="align-top">
                        <td class="p-3 whitespace-nowrap text-slate-600">
                            {{ $log->created_at?->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="p-3">{{ $causerName }}</td>
                        <td class="p-3 text-slate-700">{{ $log->description }}</td>
                        <td class="p-3 text-slate-600">
                            {{ $subjectShort }}
                            @if($log->subject_id)
                                <span class="text-slate-400">#{{ $log->subject_id }}</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @if(! empty($changes))
                                <details>
                                    <summary class="cursor-pointer text-slate-600 hover:text-slate-900">
                                        Ver cambios
                                    </summary>
                                    <pre class="mt-2 bg-slate-50 border border-slate-200 rounded p-2 text-xs overflow-x-auto">{{ json_encode($changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin actividad registrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</x-admin.layouts.admin>
