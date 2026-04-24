<x-admin.form-field name="name" label="Nombre" :value="$lodging->name" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$lodging->slug" help="URL amigable. Se genera desde el nombre si no se completa." />

<x-admin.form-field name="type" label="Tipo" type="select" required>
    @foreach(['hotel' => 'Hotel', 'casa' => 'Casa', 'camping' => 'Camping', 'hostel' => 'Hostel', 'other' => 'Otro'] as $value => $label)
        <option value="{{ $value }}" @selected(old('type', $lodging->type) === $value)>{{ $label }}</option>
    @endforeach
</x-admin.form-field>

<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$lodging->description" class="min-h-[150px]" />

<x-admin.form-field name="address" label="Dirección" :value="$lodging->address" />
<x-admin.form-field name="phone" label="Teléfono" :value="$lodging->phone" />
<x-admin.form-field name="email" label="Email" type="email" :value="$lodging->email" />
<x-admin.form-field name="website" label="Sitio web" type="url" :value="$lodging->website" />

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="latitude" label="Latitud" type="number" :value="$lodging->latitude" step="any" />
    <x-admin.form-field name="longitude" label="Longitud" type="number" :value="$lodging->longitude" step="any" />
</div>
