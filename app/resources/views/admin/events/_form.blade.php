<x-admin.form-field name="title" label="Título" :value="$event->title" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$event->slug" help="URL amigable. Se genera desde el título si no se completa." />
<x-admin.form-field name="description" label="Descripción" type="textarea" :value="$event->description" class="min-h-[150px]" />
<x-admin.form-field name="location" label="Lugar" :value="$event->location" />

<x-admin.form-field name="starts_at" label="Inicio" type="datetime-local"
    :value="optional($event->starts_at)->format('Y-m-d\TH:i')" />
<x-admin.form-field name="ends_at" label="Fin" type="datetime-local"
    :value="optional($event->ends_at)->format('Y-m-d\TH:i')" />

<x-admin.form-field name="all_day" label="Día completo" type="checkbox" :value="$event->all_day" />
<x-admin.form-field name="featured" label="Destacado" type="checkbox" :value="$event->featured" />
<x-admin.form-field name="accepts_registrations" label="Acepta inscripciones" type="checkbox" :value="$event->accepts_registrations" />

<x-admin.form-field name="external_url" label="URL externa (opcional)" :value="$event->external_url" />
<x-admin.form-field name="sort_order" label="Orden" type="number" :value="$event->sort_order" />
