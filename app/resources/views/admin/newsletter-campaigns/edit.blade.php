<x-admin.layouts.admin title="Editar: {{ $campaign->subject }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Campañas' => route('admin.newsletter-campaigns.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.newsletter-campaigns.update', $campaign) }}"
          class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.newsletter-campaigns._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @can('update', $campaign)
        <form method="POST" action="{{ route('admin.newsletter-campaigns.send', $campaign) }}"
              class="mt-4 max-w-3xl"
              onsubmit="return confirm('¿Encolar envío de la campaña?')">
            @csrf
            <button type="submit"
                    class="bg-emerald-700 text-white rounded px-4 py-2 hover:bg-emerald-600 text-sm">
                Enviar campaña
            </button>
        </form>
    @endcan
</x-admin.layouts.admin>
