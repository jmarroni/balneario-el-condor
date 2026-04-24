<?php

namespace App\Console\Commands\Etl;

use App\Models\Media;
use App\Models\Recipe;
use Illuminate\Support\Str;

class EtlRecipesCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:recipes';
    protected $description = 'Migra recetas → recipes + media';

    protected string $legacyTable = 'recetas';
    protected string $targetModel = Recipe::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->re_titulo);
        if (empty($title)) {
            return null;
        }

        return [
            'legacy_id'    => $row->re_id,
            'title'        => $title,
            'slug'         => Str::slug($this->toUtf8($row->re_keyword) ?: $title) ?: 'receta-' . $row->re_id,
            'prep_minutes' => (int) $row->re_tiempo_preparacion,
            'cook_minutes' => (int) $row->re_tiempo_coccion,
            'servings'     => $this->toUtf8($row->re_porciones),
            'cost'         => $this->toUtf8($row->re_costo),
            'ingredients'  => $this->toUtf8($row->re_ingredientes) ?? '',
            'instructions' => $this->toUtf8($row->re_preparacion) ?? '',
            'author'       => $this->toUtf8($row->re_autor),
            'published_on' => $this->parseDate($row->re_fecha),
        ];
    }

    protected function afterUpsert($recipe, object $row): void
    {
        $recipe->media()->delete();
        foreach (['re_imagen_top' => 0, 're_imagen_bottom' => 1] as $col => $order) {
            if (!empty($row->$col)) {
                Media::create([
                    'mediable_id'   => $recipe->id,
                    'mediable_type' => Recipe::class,
                    'path'          => 'legacy/recetas/' . $this->toUtf8($row->$col),
                    'sort_order'    => $order,
                    'type'          => 'image',
                ]);
            }
        }
    }
}
