<x-admin.layouts.admin title="Nueva página"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Páginas' => route('admin.pages.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.pages.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.pages._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
