<x-admin.layouts.admin title="Nueva encuesta"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Encuestas' => route('admin.surveys.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.surveys.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.surveys._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
