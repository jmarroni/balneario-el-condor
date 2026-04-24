<?php

namespace App\Console\Commands\Etl;

use App\Models\NearbyPlace;
use Illuminate\Support\Str;

class EtlNearbyCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:nearby';
    protected $description = 'Migra cercanos → nearby_places';

    protected string $legacyTable = 'cercanos';
    protected string $targetModel = NearbyPlace::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->ce_titulo);
        if (empty($title)) {
            return null;
        }

        [$lat, $lng] = $this->parseLatLng($row->ce_googlemaps);

        return [
            'legacy_id'   => $row->ce_id,
            'title'       => $title,
            'slug'        => Str::slug($this->toUtf8($row->ce_keyword) ?: $title) ?: 'cercano-' . $row->ce_id,
            'description' => $this->toUtf8($row->ce_descripcion),
            'address'     => $this->toUtf8($row->ce_direccion),
            'latitude'    => $lat,
            'longitude'   => $lng,
            'views'       => (int) ($row->ce_visitas ?? 0),
        ];
    }
}
