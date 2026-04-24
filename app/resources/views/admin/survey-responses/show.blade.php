<x-admin.layouts.admin title="Respuesta #{{ $response->id }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Encuestas' => route('admin.surveys.index'),
        $response->survey->title => route('admin.surveys.edit', $response->survey),
        'Respuestas' => route('admin.surveys.responses.index', $response->survey),
        '#' . $response->id => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500 uppercase text-xs">Opción</dt>
                <dd class="text-slate-800">#{{ $response->option_key }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Email</dt>
                <dd class="text-slate-800">{{ $response->email ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-slate-500 uppercase text-xs">Comentario</dt>
                <dd class="text-slate-800 whitespace-pre-wrap">{{ $response->comment ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Aceptó términos</dt>
                <dd class="text-slate-800">{{ $response->accepted_terms ? 'Sí' : 'No' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">IP</dt>
                <dd class="text-slate-800">{{ $response->ip_address ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Fecha</dt>
                <dd class="text-slate-800">{{ $response->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.surveys.responses.index', $response->survey) }}"
               class="text-slate-600 hover:underline text-sm">Volver</a>

            @can('delete', $response)
                <form method="POST" action="{{ route('admin.responses.destroy', $response) }}"
                      onsubmit="return confirm('¿Eliminar respuesta?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline text-sm">Eliminar</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin.layouts.admin>
