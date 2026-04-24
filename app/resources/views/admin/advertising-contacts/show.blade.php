<x-admin.layouts.admin title="Contacto #{{ $adContact->id }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Publicite' => route('admin.advertising-contacts.index'),
        '#' . $adContact->id => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-slate-500 uppercase text-xs">Nombre</dt>
                <dd class="text-slate-800">{{ $adContact->name }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Apellido</dt>
                <dd class="text-slate-800">{{ $adContact->last_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Email</dt>
                <dd class="text-slate-800">{{ $adContact->email }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Zona</dt>
                <dd class="text-slate-800">{{ $adContact->zone ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-slate-500 uppercase text-xs">Mensaje</dt>
                <dd class="text-slate-800 whitespace-pre-wrap">{{ $adContact->message }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Fecha</dt>
                <dd class="text-slate-800">{{ $adContact->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.advertising-contacts.index') }}"
               class="text-slate-600 hover:underline text-sm">Volver</a>

            @can('delete', $adContact)
                <form method="POST" action="{{ route('admin.advertising-contacts.destroy', $adContact) }}"
                      onsubmit="return confirm('¿Eliminar contacto?')" class="inline">
                    @csrf @method('DELETE')
                    <button class="text-red-600 hover:underline text-sm">Eliminar</button>
                </form>
            @endcan
        </div>
    </div>
</x-admin.layouts.admin>
