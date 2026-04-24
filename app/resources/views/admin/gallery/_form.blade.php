<x-admin.form-field name="title" label="Título" :value="$galleryImage->title" />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$galleryImage->slug" help="URL amigable. Se genera desde el título si no se completa." />
<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$galleryImage->description" />
<x-admin.form-field name="taken_on" label="Fecha de la foto" type="date" :value="optional($galleryImage->taken_on)->format('Y-m-d')" />

<div class="mb-4">
    <label for="image" class="block text-sm font-medium text-slate-700 mb-1">
        Imagen @if(! $galleryImage->exists) <span class="text-red-500">*</span> @endif
    </label>
    <input id="image" name="image" type="file" accept="image/*" class="w-full">
    @if($galleryImage->exists && $galleryImage->thumb_path)
        <div class="mt-2">
            <p class="text-xs text-slate-500 mb-1">Actual:</p>
            <img src="{{ asset('storage/' . $galleryImage->thumb_path) }}"
                 alt="{{ $galleryImage->title }}"
                 class="w-24 h-24 object-cover rounded">
        </div>
    @endif
    @error('image')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
