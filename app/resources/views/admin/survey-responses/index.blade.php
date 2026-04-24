<x-admin.layouts.admin title="Respuestas: {{ $survey->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Encuestas' => route('admin.surveys.index'),
        $survey->title => route('admin.surveys.edit', $survey),
        'Respuestas' => null,
    ]">
    <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Distribución ({{ $total }} respuestas)</h2>
        @if($total === 0)
            <p class="text-sm text-slate-500">Sin respuestas todavía.</p>
        @else
            <div class="space-y-2">
                @foreach(($survey->options ?? []) as $opt)
                    @php
                        $count = $distribution[$opt['key']] ?? 0;
                        $pct   = $total > 0 ? round($count * 100 / $total) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs text-slate-600 mb-1">
                            <span>{{ $opt['label'] }}</span>
                            <span>{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded h-3">
                            <div class="bg-slate-700 h-3 rounded" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Opción</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Comentario</th>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($responses as $r)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.responses.show', $r) }}" class="hover:underline">
                                #{{ $r->option_key }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $r->email ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ \Illuminate\Support\Str::limit($r->comment ?? '—', 50) }}</td>
                        <td class="p-3 text-slate-600">{{ $r->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $r)
                                <form method="POST" action="{{ route('admin.responses.destroy', $r) }}"
                                      onsubmit="return confirm('¿Eliminar respuesta?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin respuestas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $responses->links() }}</div>
</x-admin.layouts.admin>
