<?php

namespace App\Console\Commands\Etl;

use App\Models\NewsCategory;
use Illuminate\Support\Str;

class EtlNewsCategoriesCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:news-categories';
    protected $description = 'Migra categorias_novedades → news_categories';

    protected string $legacyTable = 'categorias_novedades';
    protected string $targetModel = NewsCategory::class;

    protected function mapRow(object $row): ?array
    {
        $name = $this->toUtf8(trim($row->nombre ?? ''));
        if ($name === '' || $name === null) return null;

        return [
            'legacy_id' => $row->id,
            'name'      => $name,
            'slug'      => Str::slug($name),
        ];
    }
}
