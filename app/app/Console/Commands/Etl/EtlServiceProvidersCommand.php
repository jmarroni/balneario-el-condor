<?php

namespace App\Console\Commands\Etl;

use App\Models\Media;
use App\Models\ServiceProvider;
use Illuminate\Support\Str;

class EtlServiceProvidersCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:service-providers';
    protected $description = 'Migra servicios → service_providers + media';

    protected string $legacyTable = 'servicios';
    protected string $targetModel = ServiceProvider::class;

    protected function mapRow(object $row): ?array
    {
        $name = $this->toUtf8($row->ser_titulo);
        if (empty($name)) {
            return null;
        }

        [$lat, $lng] = $this->parseLatLng($row->ser_googlemaps);

        return [
            'legacy_id'     => $row->ser_id,
            'name'          => $name,
            'slug'          => Str::slug($this->toUtf8($row->ser_keyword) ?: $name) ?: 'servicio-' . $row->ser_id,
            'description'   => $this->toUtf8($row->ser_descripcion),
            'contact_name'  => $this->toUtf8($row->ser_nombre_contacto),
            'contact_email' => $this->validEmail($row->ser_mail_contacto),
            'address'       => $this->toUtf8($row->ser_direccion),
            'latitude'      => $lat,
            'longitude'     => $lng,
        ];
    }

    protected function afterUpsert($sp, object $row): void
    {
        $sp->media()->delete();
        $paths = array_filter(array_map('trim', explode(',', $this->toUtf8($row->ser_imagenes) ?? '')));
        foreach ($paths as $i => $p) {
            Media::create([
                'mediable_id'   => $sp->id,
                'mediable_type' => ServiceProvider::class,
                'path'          => 'legacy/servicios/' . $p,
                'sort_order'    => $i,
                'type'          => 'image',
            ]);
        }
    }
}
