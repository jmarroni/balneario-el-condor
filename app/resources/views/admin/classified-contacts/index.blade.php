<x-admin.layouts.admin title="Contactos: {{ $classified->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Clasificados' => route('admin.classifieds.index'),
        $classified->title => route('admin.classifieds.edit', $classified),
        'Contactos' => null,
    ]">
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-left p-3">Fecha</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($contacts as $contact)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.contacts.show', $contact) }}" class="hover:underline">
                                {{ $contact->contact_name }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $contact->contact_email }}</td>
                        <td class="p-3 text-slate-600">{{ $contact->contact_phone ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $contact->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $contact)
                                <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                                      onsubmit="return confirm('¿Eliminar contacto?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin contactos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $contacts->links() }}</div>
</x-admin.layouts.admin>
