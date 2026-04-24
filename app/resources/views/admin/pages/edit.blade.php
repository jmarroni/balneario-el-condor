<x-admin.layouts.admin title="Editar: {{ $page->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Páginas' => route('admin.pages.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.pages._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @if($page->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$page" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
