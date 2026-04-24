<?php

namespace App\Console\Commands\Etl;

use App\Models\Media;
use App\Models\Venue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EtlVenuesCommand extends Command
{
    protected $signature   = 'etl:venues';
    protected $description = 'Migra gourmet + nocturnos → venues (con columna category)';

    public function handle(): int
    {
        $this->migrateGourmet();
        $this->migrateNightlife();

        return self::SUCCESS;
    }

    protected function migrateGourmet(): void
    {
        $this->info('[etl:venues] gourmet → venues');
        DB::connection('legacy')->table('gourmet')->orderBy('go_id')->chunk(500, function ($rows) {
            foreach ($rows as $row) {
                $title = $this->toUtf8($row->go_titulo);
                if (empty($title)) {
                    continue;
                }

                [$lat, $lng] = $this->parseLatLng($row->go_googlemaps);
                $venue = Venue::updateOrCreate(
                    ['legacy_id' => 'gourmet-' . $row->go_id],
                    [
                        'name'        => $title,
                        'slug'        => Str::slug($this->toUtf8($row->go_keyword) ?: $title) ?: 'gourmet-' . $row->go_id,
                        'category'    => 'gourmet',
                        'description' => $this->toUtf8($row->go_descripcion),
                        'address'     => $this->toUtf8($row->go_direccion),
                        'phone'       => $this->toUtf8($row->go_telefono),
                        'latitude'    => $lat,
                        'longitude'   => $lng,
                        'views'       => (int) ($row->go_visitas ?? 0),
                    ],
                );

                $venue->media()->delete();
                if (!empty($row->go_imagen)) {
                    Media::create([
                        'mediable_id'   => $venue->id,
                        'mediable_type' => Venue::class,
                        'path'          => 'legacy/gourmet/' . $this->toUtf8($row->go_imagen),
                        'sort_order'    => 0,
                        'type'          => 'image',
                    ]);
                }

                $extras = DB::connection('legacy')->table('gourmet_imagenes')
                    ->where('go_id', $row->go_id)->orderBy('gi_id')->get();
                foreach ($extras as $i => $img) {
                    Media::create([
                        'mediable_id'   => $venue->id,
                        'mediable_type' => Venue::class,
                        'path'          => 'legacy/gourmet/' . $this->toUtf8($img->gi_url),
                        'alt'           => $this->toUtf8($img->go_comentario),
                        'sort_order'    => $i + 1,
                        'type'          => 'image',
                    ]);
                }
            }
        });
    }

    protected function migrateNightlife(): void
    {
        $this->info('[etl:venues] nocturnos → venues');
        DB::connection('legacy')->table('nocturnos')->orderBy('no_id')->chunk(500, function ($rows) {
            foreach ($rows as $row) {
                $title = $this->toUtf8($row->no_titulo);
                if (empty($title)) {
                    continue;
                }

                [$lat, $lng] = $this->parseLatLng($row->no_googlemaps);
                $venue = Venue::updateOrCreate(
                    ['legacy_id' => 'nocturnos-' . $row->no_id],
                    [
                        'name'        => $title,
                        'slug'        => Str::slug($this->toUtf8($row->no_keyword) ?: $title) ?: 'nocturno-' . $row->no_id,
                        'category'    => 'nightlife',
                        'description' => $this->toUtf8($row->no_descripcion),
                        'address'     => $this->toUtf8($row->no_direccion),
                        'latitude'    => $lat,
                        'longitude'   => $lng,
                        'views'       => (int) ($row->no_visitas ?? 0),
                    ],
                );

                $venue->media()->delete();
                if (!empty($row->no_imagen)) {
                    Media::create([
                        'mediable_id'   => $venue->id,
                        'mediable_type' => Venue::class,
                        'path'          => 'legacy/nocturnos/' . $this->toUtf8($row->no_imagen),
                        'sort_order'    => 0,
                        'type'          => 'image',
                    ]);
                }
            }
        });
    }

    protected function toUtf8(?string $v): ?string
    {
        if ($v === null || $v === '') {
            return $v;
        }
        if (mb_check_encoding($v, 'UTF-8') && ! preg_match('/[\xC2-\xDF][\x80-\xBF]/', $v)) {
            return $v;
        }

        return mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1');
    }

    protected function parseLatLng(?string $v): array
    {
        if (empty($v)) {
            return [null, null];
        }
        $p = explode(',', str_replace(' ', '', $v));
        if (count($p) !== 2) {
            return [null, null];
        }
        $lat = (float) $p[0];
        $lng = (float) $p[1];
        if ($lat === 0.0 || $lng === 0.0) {
            return [null, null];
        }

        return [$lat, $lng];
    }
}
