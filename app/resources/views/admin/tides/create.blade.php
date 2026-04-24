<x-admin.layouts.admin title="Nueva marea"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Mareas' => route('admin.tides.index'),
        'Nueva' => null,
    ]">
    <form method="POST" action="{{ route('admin.tides.store') }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf
        @include('admin.tides._form')
        <x-admin.submit-button label="Crear" />
    </form>
</x-admin.layouts.admin>
