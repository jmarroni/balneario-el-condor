<x-admin.layouts.admin title="Editar: {{ $rental->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Alquileres' => route('admin.rentals.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.rentals.update', $rental) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.rentals._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @if($rental->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$rental" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
