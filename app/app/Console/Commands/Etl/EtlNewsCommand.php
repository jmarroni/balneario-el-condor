<?php

namespace App\Console\Commands\Etl;

use App\Models\Media;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Support\Str;

class EtlNewsCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:news';
    protected $description = 'Migra novedades → news + media';

    protected string $legacyTable = 'novedades';
    protected string $targetModel = News::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->nov_titulo);
        if (empty($title)) return null;

        $category = NewsCategory::where('legacy_id', $row->cn_id ?: 1)->first();

        return [
            'legacy_id'         => $row->nov_id,
            'title'             => $title,
            'slug'              => Str::slug($this->toUtf8($row->nov_keyword) ?: $title) ?: 'novedad-' . $row->nov_id,
            'excerpt'           => null,
            'body'              => $this->toUtf8($row->nov_descripcion),
            'news_category_id'  => $category?->id,
            'published_at'      => $this->parseDate($row->nov_fechahora),
            'views'             => (int) $row->nov_visitas,
        ];
    }

    protected function afterUpsert($news, object $row): void
    {
        // Limpiar media previa para idempotencia
        $news->media()->delete();

        // `nov_imagenes` es CSV con rutas separadas por coma
        $paths = array_filter(array_map('trim', explode(',', $this->toUtf8($row->nov_imagenes) ?? '')));
        foreach ($paths as $i => $path) {
            Media::create([
                'mediable_id'   => $news->id,
                'mediable_type' => News::class,
                'path'          => 'legacy/novedades/' . $path,
                'sort_order'    => $i,
                'type'          => 'image',
            ]);
        }
    }
}
