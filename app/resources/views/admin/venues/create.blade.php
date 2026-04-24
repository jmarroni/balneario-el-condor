<x-admin.layouts.admin title="Nuevo local"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Locales' => route('admin.venues.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.venues.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.venues._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
