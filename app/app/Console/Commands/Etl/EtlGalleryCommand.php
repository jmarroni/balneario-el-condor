<?php

namespace App\Console\Commands\Etl;

use App\Models\GalleryImage;
use Illuminate\Support\Str;

class EtlGalleryCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:gallery';
    protected $description = 'Migra imagenes → gallery_images';

    protected string $legacyTable = 'imagenes';
    protected string $targetModel = GalleryImage::class;
    protected int $chunkSize       = 200; // ~1956 filas

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->im_titulo) ?: 'Imagen ' . $row->im_id;
        $file  = $this->toUtf8($row->im_imagen);
        if (empty($file)) {
            return null;
        }

        return [
            'legacy_id'     => $row->im_id,
            'title'         => $title,
            'slug'          => Str::slug($this->toUtf8($row->im_keyword) ?: $title) . '-' . $row->im_id,
            'description'   => $this->toUtf8($row->im_descripcion),
            'path'          => 'legacy/imagenes/' . $file,
            'thumb_path'    => $row->im_thumb ? 'legacy/imagenes/thumbs/' . $this->toUtf8($row->im_thumb) : null,
            'original_path' => $row->img_orig ? 'legacy/imagenes/original/' . $this->toUtf8($row->img_orig) : null,
            'taken_on'      => $this->parseDate($row->im_fecha),
            'views'         => (int) ($row->im_visitas ?? 0),
        ];
    }
}
