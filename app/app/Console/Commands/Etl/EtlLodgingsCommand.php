<?php

namespace App\Console\Commands\Etl;

use App\Models\Lodging;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EtlLodgingsCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:lodgings';
    protected $description = 'Migra hospedaje + hospedaje_imagenes → lodgings + media';

    protected string $legacyTable = 'hospedaje';
    protected string $targetModel = Lodging::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->ho_titulo);
        if (empty($title)) {
            return null;
        }

        [$lat, $lng] = $this->parseLatLng($row->ho_googlemaps);
        $type = match ((int) $row->ho_tipo) {
            1 => 'hotel',
            2 => 'casa',
            3 => 'camping',
            default => 'other',
        };

        return [
            'legacy_id'   => $row->ho_id,
            'name'        => $title,
            'slug'        => Str::slug($this->toUtf8($row->ho_keyword) ?: $title) ?: 'hospedaje-' . $row->ho_id,
            'type'        => $type,
            'description' => $this->toUtf8($row->ho_descripcion),
            'address'     => $this->toUtf8($row->ho_direccion),
            'phone'       => $this->toUtf8($row->ho_telefono),
            'email'       => $this->validEmail($row->ho_mail),
            'website'     => $this->toUtf8($row->ho_web),
            'latitude'    => $lat,
            'longitude'   => $lng,
            'views'       => (int) ($row->ho_visitas ?? 0),
        ];
    }

    protected function afterUpsert($lodging, object $row): void
    {
        $lodging->media()->delete();

        // Imagen principal en ho_imagen
        if (!empty($row->ho_imagen)) {
            Media::create([
                'mediable_id'   => $lodging->id,
                'mediable_type' => Lodging::class,
                'path'          => 'legacy/hospedaje/' . $this->toUtf8($row->ho_imagen),
                'sort_order'    => 0,
                'type'          => 'image',
            ]);
        }

        // Imágenes adicionales en hospedaje_imagenes
        $extras = DB::connection('legacy')->table('hospedaje_imagenes')
            ->where('ho_id', $row->ho_id)
            ->orderBy('hi_id')
            ->get();
        foreach ($extras as $i => $img) {
            Media::create([
                'mediable_id'   => $lodging->id,
                'mediable_type' => Lodging::class,
                'path'          => 'legacy/hospedaje/' . $this->toUtf8($img->ho_url),
                'alt'           => $this->toUtf8($img->ho_comentario),
                'sort_order'    => $i + 1,
                'type'          => 'image',
            ]);
        }
    }
}
