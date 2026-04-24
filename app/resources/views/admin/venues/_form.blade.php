<x-admin.form-field name="name" label="Nombre" :value="$venue->name" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$venue->slug" help="URL amigable. Se genera desde el nombre si no se completa." />

<x-admin.form-field name="category" label="Categoría" type="select" required>
    @foreach(['gourmet' => 'Gourmet', 'nightlife' => 'Nocturnos'] as $value => $label)
        <option value="{{ $value }}" @selected(old('category', $venue->category) === $value)>{{ $label }}</option>
    @endforeach
</x-admin.form-field>

<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$venue->description" class="min-h-[150px]" />

<x-admin.form-field name="address" label="Dirección" :value="$venue->address" />
<x-admin.form-field name="phone" label="Teléfono" :value="$venue->phone" />

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="latitude" label="Latitud" type="number" :value="$venue->latitude" step="any" />
    <x-admin.form-field name="longitude" label="Longitud" type="number" :value="$venue->longitude" step="any" />
</div>
