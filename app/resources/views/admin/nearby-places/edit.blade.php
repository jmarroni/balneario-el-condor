<x-admin.layouts.admin title="Editar: {{ $place->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Lugares cercanos' => route('admin.nearby-places.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.nearby-places.update', $place) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.nearby-places._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
