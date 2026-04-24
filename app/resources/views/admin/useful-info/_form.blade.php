<x-admin.form-field name="title" label="Título" :value="$item->title" required />

<x-admin.form-field name="phone" label="Teléfono" :value="$item->phone" />
<x-admin.form-field name="email" label="Email" type="email" :value="$item->email" />
<x-admin.form-field name="website" label="Sitio web" type="url" :value="$item->website" />
<x-admin.form-field name="address" label="Dirección" :value="$item->address" />

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="latitude" label="Latitud" type="number" :value="$item->latitude" step="any" />
    <x-admin.form-field name="longitude" label="Longitud" type="number" :value="$item->longitude" step="any" />
</div>

<x-admin.form-field name="sort_order" label="Orden" type="number" :value="$item->sort_order" />
