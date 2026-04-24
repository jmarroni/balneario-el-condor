<?php

namespace App\Console\Commands\Etl;

use App\Models\UsefulInfo;

class EtlUsefulInfoCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:useful-info';
    protected $description = 'Migra informacionutil → useful_info';

    protected string $legacyTable = 'informacionutil';
    protected string $targetModel = UsefulInfo::class;

    protected function legacyPrimaryKey(): string
    {
        return 'idinformacionutil';
    }

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->titulo);
        if (empty($title)) {
            return null;
        }

        $lat = is_numeric($row->lat) ? (float) $row->lat : null;
        $lng = is_numeric($row->lon) ? (float) $row->lon : null;

        return [
            'legacy_id'  => $row->idinformacionutil,
            'title'      => $title,
            'phone'      => $this->toUtf8($row->telefono),
            'website'    => $this->toUtf8($row->web),
            'email'      => $this->validEmail($row->mail),
            'address'    => $this->toUtf8($row->direccion),
            'latitude'   => $lat,
            'longitude'  => $lng,
            'sort_order' => (int) ($row->orden ?? 0),
        ];
    }
}
