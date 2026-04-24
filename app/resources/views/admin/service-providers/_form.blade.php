<x-admin.form-field name="name" label="Nombre" :value="$provider->name" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$provider->slug" help="URL amigable. Se genera desde el nombre si no se completa." />

<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$provider->description" class="min-h-[150px]" />

<x-admin.form-field name="contact_name" label="Nombre de contacto" :value="$provider->contact_name" />
<x-admin.form-field name="contact_email" label="Email de contacto" type="email" :value="$provider->contact_email" />
<x-admin.form-field name="phone" label="Teléfono" :value="$provider->phone" />
<x-admin.form-field name="address" label="Dirección" :value="$provider->address" />

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="latitude" label="Latitud" type="number" :value="$provider->latitude" step="any" />
    <x-admin.form-field name="longitude" label="Longitud" type="number" :value="$provider->longitude" step="any" />
</div>
