<x-admin.layouts.admin title="Editar: {{ $user->name }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Usuarios' => route('admin.users.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.users._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
