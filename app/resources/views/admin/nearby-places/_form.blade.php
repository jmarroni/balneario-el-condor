<x-admin.form-field name="title" label="Título" :value="$place->title" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$place->slug" help="URL amigable. Se genera desde el título si no se completa." />

<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$place->description" class="min-h-[150px]" />

<x-admin.form-field name="address" label="Dirección" :value="$place->address" />

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="latitude" label="Latitud" type="number" :value="$place->latitude" step="any" />
    <x-admin.form-field name="longitude" label="Longitud" type="number" :value="$place->longitude" step="any" />
</div>
