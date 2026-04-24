<x-admin.layouts.admin title="Nuevo evento"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Eventos' => route('admin.events.index'),
        'Nuevo' => null,
    ]">
    <form method="POST" action="{{ route('admin.events.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.events._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
