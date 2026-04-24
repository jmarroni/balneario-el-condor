<?php

namespace App\Console\Commands\Etl;

use App\Models\Media;
use App\Models\Rental;
use Illuminate\Support\Str;

class EtlRentalsCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:rentals';
    protected $description = 'Migra alquiler → rentals + media (uim_imagen0..4)';

    protected string $legacyTable = 'alquiler';
    protected string $targetModel = Rental::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->uim_titulo);
        if (empty($title)) {
            return null;
        }

        return [
            'legacy_id'    => $row->uim_id,
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . $row->uim_id,
            'places'       => (int) ($row->uim_plazas ?? 0),
            'phone'        => $this->toUtf8($row->uim_telefono),
            'email'        => $this->validEmail($row->uim_mail),
            'address'      => $this->toUtf8($row->uim_direccion),
            'contact_name' => $this->toUtf8($row->uim_contacto),
            'description'  => $this->toUtf8($row->uim_descripcion),
        ];
    }

    protected function afterUpsert($rental, object $row): void
    {
        $rental->media()->delete();
        for ($i = 0; $i <= 4; $i++) {
            $col  = "uim_imagen{$i}";
            $path = $this->toUtf8($row->$col ?? null);
            if (!empty($path)) {
                Media::create([
                    'mediable_id'   => $rental->id,
                    'mediable_type' => Rental::class,
                    'path'          => 'legacy/alquiler/' . $path,
                    'sort_order'    => $i,
                    'type'          => 'image',
                ]);
            }
        }
    }
}
