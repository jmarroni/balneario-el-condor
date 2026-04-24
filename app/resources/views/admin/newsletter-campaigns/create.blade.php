<x-admin.layouts.admin title="Nueva campaña"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Campañas' => route('admin.newsletter-campaigns.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.newsletter-campaigns.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.newsletter-campaigns._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
