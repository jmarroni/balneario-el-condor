<x-admin.layouts.admin title="Prestadores de servicios"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Prestadores' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\ServiceProvider::class)
            <a href="{{ route('admin.service-providers.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo prestador
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Contacto</th>
                    <th class="text-left p-3">Teléfono</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($providers as $provider)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.service-providers.edit', $provider) }}" class="hover:underline">
                                {{ $provider->name }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $provider->contact_name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $provider->phone ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $provider->contact_email ?? '—' }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $provider)
                                <form method="POST" action="{{ route('admin.service-providers.destroy', $provider) }}"
                                      onsubmit="return confirm('¿Eliminar prestador?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin prestadores.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $providers->links() }}</div>
</x-admin.layouts.admin>
