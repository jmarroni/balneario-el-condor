@php
    $isNew = ! $user->exists;
    $currentRole = old('role', $user->roles->first()?->name);
@endphp

<x-admin.form-field name="name" label="Nombre" :value="$user->name" required />
<x-admin.form-field name="email" label="Email" type="email" :value="$user->email" required />

<x-admin.form-field name="password" label="{{ $isNew ? 'Contraseña' : 'Contraseña (dejar en blanco para no cambiar)' }}"
    type="password" :value="null" :required="$isNew" />

<x-admin.form-field name="password_confirmation" label="Confirmar contraseña"
    type="password" :value="null" :required="$isNew" />

<x-admin.form-field name="role" label="Rol" type="select" required>
    @foreach(['admin' => 'Administrador', 'editor' => 'Editor', 'moderator' => 'Moderador'] as $k => $label)
        <option value="{{ $k }}" @selected($currentRole === $k)>{{ $label }}</option>
    @endforeach
</x-admin.form-field>
