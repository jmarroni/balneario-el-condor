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

    @if($lodging->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$lodging" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
