<x-admin.layouts.admin title="Nueva noticia"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Noticias' => route('admin.news.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.news.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.news._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
