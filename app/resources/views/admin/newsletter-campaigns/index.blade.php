<x-admin.layouts.admin title="Campañas de newsletter"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Campañas' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\NewsletterCampaign::class)
            <a href="{{ route('admin.newsletter-campaigns.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva campaña
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Asunto</th>
                    <th class="text-left p-3">Estado</th>
                    <th class="text-left p-3">Programada</th>
                    <th class="text-left p-3">Enviada</th>
                    <th class="text-right p-3">Envíos</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($campaigns as $c)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.newsletter-campaigns.edit', $c) }}" class="hover:underline">
                                {{ $c->subject }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $c->status }}</td>
                        <td class="p-3 text-slate-600">{{ $c->scheduled_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $c->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="p-3 text-slate-600 text-right">{{ $c->sent_count }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $c)
                                <form method="POST" action="{{ route('admin.newsletter-campaigns.destroy', $c) }}"
                                      onsubmit="return confirm('¿Eliminar campaña?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-slate-500">Sin campañas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $campaigns->links() }}</div>
</x-admin.layouts.admin>
