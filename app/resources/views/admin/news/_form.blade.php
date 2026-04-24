<x-admin.form-field name="title" label="Título" :value="$news->title" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$news->slug" help="URL amigable. Se genera desde el título si no se completa." />
<x-admin.form-field name="body" label="Cuerpo" type="textarea" :value="$news->body" required class="min-h-[200px]" />

<x-admin.form-field name="news_category_id" label="Categoría" type="select">
    <option value="">— Sin categoría —</option>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(old('news_category_id', $news->news_category_id) == $cat->id)>{{ $cat->name }}</option>
    @endforeach
</x-admin.form-field>

<x-admin.form-field name="video_url" label="URL de video (opcional)" :value="$news->video_url" help="YouTube, Vimeo, etc." />

<x-admin.form-field name="published_at" label="Fecha de publicación" type="datetime-local"
    :value="optional($news->published_at)->format('Y-m-d\TH:i')" />
