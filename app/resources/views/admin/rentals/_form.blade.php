<x-admin.form-field name="title" label="Título" :value="$rental->title" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$rental->slug" help="URL amigable. Se genera desde el título si no se completa." />

<x-admin.form-field name="places" label="Plazas" type="number" :value="$rental->places" />
<x-admin.form-field name="contact_name" label="Nombre de contacto" :value="$rental->contact_name" />
<x-admin.form-field name="phone" label="Teléfono" :value="$rental->phone" />
<x-admin.form-field name="email" label="Email" type="email" :value="$rental->email" />
<x-admin.form-field name="address" label="Dirección" :value="$rental->address" />
<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$rental->description" class="min-h-[150px]" />
