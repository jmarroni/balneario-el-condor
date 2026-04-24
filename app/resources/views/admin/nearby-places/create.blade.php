<x-admin.layouts.admin title="Nuevo lugar cercano"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Lugares cercanos' => route('admin.nearby-places.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.nearby-places.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.nearby-places._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
