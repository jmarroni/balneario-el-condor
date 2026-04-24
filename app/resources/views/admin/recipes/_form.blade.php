<x-admin.form-field name="title" label="Título" :value="$recipe->title" required />
<x-admin.form-field name="slug" label="Slug (auto si vacío)" :value="$recipe->slug" help="URL amigable. Se genera desde el título si no se completa." />

<x-admin.form-field name="prep_minutes" label="Minutos de preparación" type="number" :value="$recipe->prep_minutes" />
<x-admin.form-field name="cook_minutes" label="Minutos de cocción" type="number" :value="$recipe->cook_minutes" />
<x-admin.form-field name="servings" label="Porciones" :value="$recipe->servings" />
<x-admin.form-field name="cost" label="Costo" :value="$recipe->cost" help="Ej: Bajo, Medio, Alto." />

<x-admin.form-field name="ingredients" label="Ingredientes" type="textarea" :value="$recipe->ingredients" required class="min-h-[180px]" />
<x-admin.form-field name="instructions" label="Instrucciones" type="textarea" :value="$recipe->instructions" required class="min-h-[250px]" />

<x-admin.form-field name="author" label="Autor" :value="$recipe->author" />
<x-admin.form-field name="published_on" label="Fecha de publicación" type="date"
    :value="optional($recipe->published_on)->format('Y-m-d')" />
