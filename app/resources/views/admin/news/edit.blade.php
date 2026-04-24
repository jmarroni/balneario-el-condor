<x-admin.layouts.admin title="Editar: {{ $news->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Noticias' => route('admin.news.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.news.update', $news) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.news._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @if($news->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$news" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
