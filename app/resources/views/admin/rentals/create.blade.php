<x-admin.layouts.admin title="Nuevo alquiler"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Alquileres' => route('admin.rentals.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.rentals.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.rentals._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
