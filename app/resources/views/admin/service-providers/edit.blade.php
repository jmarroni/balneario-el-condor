<x-admin.layouts.admin title="Editar: {{ $provider->name }}"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Prestadores' => route('admin.service-providers.index'),
        'Editar' => null,
    ]">
    <form method="POST" action="{{ route('admin.service-providers.update', $provider) }}" class="bg-white rounded shadow p-6 max-w-3xl">
        @csrf @method('PUT')
        @include('admin.service-providers._form')
        <x-admin.submit-button label="Actualizar" />
    </form>

    @if($provider->exists)
        <div class="max-w-3xl">
            <x-admin.media-manager :mediable="$provider" class="mt-6" />
        </div>
    @endif
</x-admin.layouts.admin>
