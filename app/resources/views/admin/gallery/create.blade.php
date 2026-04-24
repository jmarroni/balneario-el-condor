<x-admin.layouts.admin title="Nueva imagen"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Galería' => route('admin.gallery.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data"
          class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.gallery._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
