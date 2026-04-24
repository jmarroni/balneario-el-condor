<x-admin.layouts.admin title="Nuevo alojamiento"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Alojamientos' => route('admin.lodgings.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.lodgings.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.lodgings._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
