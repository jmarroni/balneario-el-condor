<x-admin.layouts.admin title="Inscripción #{{ $registration->id }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Eventos' => route('admin.events.index'),
        $registration->event->title => route('admin.events.edit', $registration->event),
        'Inscripciones' => route('admin.events.registrations.index', $registration->event),
        '#' . $registration->id => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500 uppercase text-xs">Nombre</dt>
                <dd class="text-slate-800">{{ $registration->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Apellido</dt>
                <dd class="text-slate-800">{{ $registration->last_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Email</dt>
                <dd class="text-slate-800">{{ $registration->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Teléfono</dt>
                <dd class="text-slate-800">{{ $registration->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Provincia</dt>
                <dd class="text-slate-800">{{ $registration->province ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Ciudad</dt>
                <dd class="text-slate-800">{{ $registration->city ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-slate-500 uppercase text-xs">Comentarios</dt>
                <dd class="text-slate-800 whitespace-pre-wrap">{{ $registration->comments ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">IP</dt>
                <dd class="text-slate-800">{{ $registration->ip_address ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Fecha</dt>
                <dd class="text-slate-800">{{ $registration->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        @if(!empty($registration->extra_data))
            <div class="mt-6">
                <h3 class="text-sm font-semibold text-slate-700 mb-2">Datos adicionales</h3>
                <pre class="bg-slate-50 rounded p-3 text-xs text-slate-800 overflow-x-auto">{{ json_encode($registration->extra_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endif

        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.events.registrations.index', $registration->event) }}"
               class="text-slate-600 hover:underline text-sm">Volver</a>

            @can('delete', $registration)
                <form method="POST" action="{{ route('admin.registrations.destroy', $registration) }}"
                      onsubmit="return confirm('¿Eliminar inscripción?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline text-sm">Eliminar</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin.layouts.admin>
