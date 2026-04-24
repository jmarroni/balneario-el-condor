<x-admin.layouts.admin title="Editar: {{ $survey->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Encuestas' => route('admin.surveys.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.surveys.update', $survey) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.surveys._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
