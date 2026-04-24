<x-admin.layouts.admin title="Nuevo usuario"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Usuarios' => route('admin.users.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.users._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
