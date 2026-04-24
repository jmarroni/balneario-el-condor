<x-admin.layouts.admin title="Contacto #{{ $contact->id }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Clasificados' => route('admin.classifieds.index'),
        $contact->classified->title => route('admin.classifieds.edit', $contact->classified),
        'Contactos' => route('admin.classifieds.contacts.index', $contact->classified),
        '#' . $contact->id => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500 uppercase text-xs">Nombre</dt>
                <dd class="text-slate-800">{{ $contact->contact_name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Email</dt>
                <dd class="text-slate-800">{{ $contact->contact_email }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Teléfono</dt>
                <dd class="text-slate-800">{{ $contact->contact_phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Email destino</dt>
                <dd class="text-slate-800">{{ $contact->destination_email ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-slate-500 uppercase text-xs">Mensaje</dt>
                <dd class="text-slate-800 whitespace-pre-wrap">{{ $contact->message ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">IP</dt>
                <dd class="text-slate-800">{{ $contact->ip_address ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Fecha</dt>
                <dd class="text-slate-800">{{ $contact->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.classifieds.contacts.index', $contact->classified) }}"
               class="text-slate-600 hover:underline text-sm">Volver</a>

            @can('delete', $contact)
                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                      onsubmit="return confirm('¿Eliminar contacto?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline text-sm">Eliminar</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin.layouts.admin>
