<x-admin.layouts.admin title="Nuevo clasificado"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Clasificados' => route('admin.classifieds.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.classifieds.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.classifieds._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
