<x-admin.layouts.admin title="Editar: {{ $lodging->name }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Alojamientos' => route('admin.lodgings.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.lodgings.update', $lodging) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.lodgings._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
