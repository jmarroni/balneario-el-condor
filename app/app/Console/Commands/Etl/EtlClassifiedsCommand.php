<?php

namespace App\Console\Commands\Etl;

use App\Models\Classified;
use App\Models\ClassifiedCategory;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EtlClassifiedsCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:classifieds';
    protected $description = 'Migra clasificados + clasificado_imagenes → classifieds + media';

    protected string $legacyTable = 'clasificados';
    protected string $targetModel = Classified::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->cla_titulo);
        if (empty($title)) {
            return null;
        }

        $catName = $this->toUtf8(trim($row->cla_categoria ?? ''));
        $cat = $catName ? ClassifiedCategory::where('slug', Str::slug($catName))->first() : null;

        [$lat, $lng] = $this->parseLatLng($row->cla_googlemaps);

        return [
            'legacy_id'              => $row->cla_id,
            'title'                  => $title,
            'slug'                   => Str::slug($this->toUtf8($row->cla_keyword) ?: $title) ?: 'clasificado-' . $row->cla_id,
            'description'            => (string) $this->toUtf8($row->cla_descripcion),
            'classified_category_id' => $cat?->id,
            'contact_name'           => $this->toUtf8($row->cla_nombre_contacto),
            'contact_email'          => $this->validEmail($row->cla_mail_contacto),
            'address'                => $this->toUtf8($row->cla_direccion),
            'latitude'               => $lat,
            'longitude'              => $lng,
            'video_url'              => $this->toUtf8($row->cla_video) ?: null,
            'views'                  => (int) $row->cla_visitas,
            'published_at'           => $this->parseDate($row->cla_fechahora),
        ];
    }

    protected function afterUpsert($classified, object $row): void
    {
        $classified->media()->delete();

        $imgs = DB::connection('legacy')->table('clasificado_imagenes')
            ->where('cla_id', $row->cla_id)->first();
        if (! $imgs) {
            return;
        }

        for ($i = 0; $i <= 4; $i++) {
            $col  = "cla_imagen{$i}";
            $path = $this->toUtf8($imgs->$col ?? null);
            if (! empty($path)) {
                Media::create([
                    'mediable_id'   => $classified->id,
                    'mediable_type' => Classified::class,
                    'path'          => 'legacy/clasificados/' . $path,
                    'sort_order'    => $i,
                    'type'          => 'image',
                ]);
            }
        }
    }
}
