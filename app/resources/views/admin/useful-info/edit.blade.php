<x-admin.layouts.admin title="Editar: {{ $item->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Información útil' => route('admin.useful-info.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.useful-info.update', $item) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.useful-info._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
