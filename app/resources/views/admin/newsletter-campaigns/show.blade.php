<x-admin.layouts.admin title="Campaña #{{ $campaign->id }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Campañas' => route('admin.newsletter-campaigns.index'),
        $campaign->subject => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
            <div>
                <dt class="text-slate-500 uppercase text-xs">Asunto</dt>
                <dd class="text-slate-800">{{ $campaign->subject }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Estado</dt>
                <dd class="text-slate-800">{{ $campaign->status }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Programada</dt>
                <dd class="text-slate-800">{{ $campaign->scheduled_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Enviada</dt>
                <dd class="text-slate-800">{{ $campaign->sent_at?->format('d/m/Y H:i') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500 uppercase text-xs">Envíos</dt>
                <dd class="text-slate-800">{{ $campaign->sent_count }}</dd>
            </div>
        </dl>

        <div class="mt-4">
            <h3 class="text-sm font-semibold text-slate-700 mb-2">Cuerpo HTML</h3>
            <pre class="bg-slate-50 rounded p-3 text-xs text-slate-800 overflow-x-auto whitespace-pre-wrap">{{ $campaign->body_html }}</pre>
        </div>

        <div class="mt-6 flex gap-2">
            <a href="{{ route('admin.newsletter-campaigns.edit', $campaign) }}"
               class="text-slate-600 hover:underline text-sm">Editar</a>
        </div>
    </div>
</x-admin.layouts.admin>
