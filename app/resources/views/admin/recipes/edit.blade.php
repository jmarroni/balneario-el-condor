<x-admin.layouts.admin title="Editar: {{ $recipe->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Recetas' => route('admin.recipes.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.recipes.update', $recipe) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.recipes._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @if($recipe->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$recipe" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
