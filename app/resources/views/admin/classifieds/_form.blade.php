<x-admin.form-field name="title" label="Título" :value="$classified->title" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$classified->slug" help="URL amigable. Se genera desde el título si no se completa." />

<x-admin.form-field name="classified_category_id" label="Categoría" type="select">
    <option value="">— Sin categoría —</option>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(old('classified_category_id', $classified->classified_category_id) == $cat->id)>{{ $cat->name }}</option>
    @endforeach
</x-admin.form-field>

<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$classified->description" required class="min-h-[150px]" />

<x-admin.form-field name="contact_name" label="Nombre de contacto" :value="$classified->contact_name" />
<x-admin.form-field name="contact_email" label="Email de contacto" type="email" :value="$classified->contact_email" />
<x-admin.form-field name="address" label="Dirección" :value="$classified->address" />

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="latitude" label="Latitud" type="number" :value="$classified->latitude" step="any" />
    <x-admin.form-field name="longitude" label="Longitud" type="number" :value="$classified->longitude" step="any" />
</div>

<x-admin.form-field name="video_url" label="URL de video (opcional)" :value="$classified->video_url" help="YouTube, Vimeo, etc." />

<x-admin.form-field name="published_at" label="Fecha de publicación" type="datetime-local"
    :value="optional($classified->published_at)->format('Y-m-d\TH:i')" />
