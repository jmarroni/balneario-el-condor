<x-admin.layouts.admin title="Editar marea: {{ $tide->date?->format('d/m/Y') }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Mareas' => route('admin.tides.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.tides.update', $tide) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.tides._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
