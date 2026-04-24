<x-admin.layouts.admin title="Nueva receta"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Recetas' => route('admin.recipes.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.recipes.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.recipes._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
