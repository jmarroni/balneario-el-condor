<x-admin.form-field name="title" label="Título" :value="$page->title" required />
<x-admin.form-field name="slug" label="Slug" :value="$page->slug" required help="URL estable — no se autogenera, debe ser único." />
<x-admin.form-field name="content" label="Contenido" type="textarea" :value="$page->content" class="min-h-[250px]" />
<x-admin.form-field name="meta_description" label="Meta descripción (SEO)" type="textarea" :value="$page->meta_description" />
<x-admin.form-field name="published" label="Publicada" type="checkbox" :value="$page->published" />
