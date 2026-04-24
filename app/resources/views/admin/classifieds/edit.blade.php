<x-admin.layouts.admin title="Editar: {{ $classified->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Clasificados' => route('admin.classifieds.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.classifieds.update', $classified) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.classifieds._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @if($classified->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$classified" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
