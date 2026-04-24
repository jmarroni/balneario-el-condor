<x-admin.layouts.admin title="Editar: {{ $event->title }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Eventos' => route('admin.events.index'),
        'Editar' => null,
    ]">
    @if($event->accepts_registrations && $event->exists)
        <div class="mb-4">
            <a href="{{ route('admin.events.registrations.index', $event) }}"
               class="inline-block text-sm text-slate-700 underline hover:text-slate-900">
                Ver inscripciones
            </a>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.events.update', $event) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.events._form')
        <x-admin.submit-button label="Actualizar" />
    </form>
</x-admin.layouts.admin>
