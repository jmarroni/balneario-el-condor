<x-admin.layouts.admin title="Mensaje #{{ $message->id }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Mensajes' => route('admin.contact-messages.index'),
        '#' . $message->id => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500 uppercase text-xs">Nombre</dt>
                <dd class="text-slate-800">{{ $message->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Email</dt>
                <dd class="text-slate-800">{{ $message->email }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Teléfono</dt>
                <dd class="text-slate-800">{{ $message->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Asunto</dt>
                <dd class="text-slate-800">{{ $message->subject ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-slate-500 uppercase text-xs">Mensaje</dt>
                <dd class="text-slate-800 whitespace-pre-wrap">{{ $message->message }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">IP</dt>
                <dd class="text-slate-800">{{ $message->ip_address ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Fecha</dt>
                <dd class="text-slate-800">{{ $message->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Leído</dt>
                <dd class="text-slate-800">{{ $message->read ? 'Sí' : 'No' }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex gap-3">
            <a href="{{ route('admin.contact-messages.index') }}"
               class="text-slate-600 hover:underline text-sm">Volver</a>

            @can('update', $message)
                <form method="POST" action="{{ route('admin.contact-messages.mark-read', $message) }}" class="inline">
                    @csrf @method('PATCH')
                    <button class="text-slate-700 hover:underline text-sm">
                        {{ $message->read ? 'Marcar como no leído' : 'Marcar como leído' }}
                    </button>
                </form>
            @endcan

            @can('delete', $message)
                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}"
                      onsubmit="return confirm('¿Eliminar mensaje?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline text-sm">Eliminar</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin.layouts.admin>
