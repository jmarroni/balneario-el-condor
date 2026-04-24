<x-admin.layouts.admin title="Nuevo prestador"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Prestadores' => route('admin.service-providers.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.service-providers.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.service-providers._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
