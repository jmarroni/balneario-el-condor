@props(['recipe'])
@php
    use Illuminate\Support\Str;

    $ingredients = collect(preg_split("/\r\n|\n/u", trim((string) $recipe->ingredients)))
        ->map(fn ($i) => trim(ltrim($i, "-•· \t")))
        ->filter(fn ($i) => $i !== '')
        ->values()
        ->all();

    $steps = collect(preg_split("/\r\n\r\n|\r\n|\n\n|\n/u", trim((string) $recipe->instructions)))
        ->map(fn ($s) => trim($s))
        ->filter(fn ($s) => $s !== '')
        ->values()
        ->map(fn ($s) => ['@type' => 'HowToStep', 'text' => $s])
        ->all();

    $data = [
        '@context'           => 'https://schema.org',
        '@type'              => 'Recipe',
        'name'               => $recipe->title,
        'url'                => route('recetas.show', $recipe),
        'description'        => Str::limit(strip_tags((string) $recipe->instructions), 200),
        'author'             => $recipe->author ? ['@type' => 'Person', 'name' => $recipe->author] : null,
        'datePublished'      => optional($recipe->published_on)?->toDateString(),
        'recipeIngredient'   => $ingredients ?: null,
        'recipeInstructions' => $steps ?: null,
        'recipeYield'        => $recipe->servings,
        'prepTime'           => $recipe->prep_minutes ? 'PT'.(int) $recipe->prep_minutes.'M' : null,
        'cookTime'           => $recipe->cook_minutes ? 'PT'.(int) $recipe->cook_minutes.'M' : null,
    ];

    $data = array_filter($data, fn ($v) => $v !== null && $v !== '' && $v !== []);
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
