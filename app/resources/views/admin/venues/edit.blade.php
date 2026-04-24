<x-admin.layouts.admin title="Editar: {{ $venue->name }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Locales' => route('admin.venues.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.venues.update', $venue) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.venues._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
