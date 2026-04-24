<x-admin.layouts.admin title="Nueva información útil"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Información útil' => route('admin.useful-info.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.useful-info.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.useful-info._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
