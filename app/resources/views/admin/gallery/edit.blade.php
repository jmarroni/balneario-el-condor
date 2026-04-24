<x-admin.layouts.admin title="Editar imagen"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Galería' => route('admin.gallery.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.gallery.update', $galleryImage) }}" enctype="multipart/form-data"
          class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.gallery._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
