<?php

namespace App\Console\Commands\Etl;

use App\Models\Tide;

class EtlTidesCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:tides';
    protected $description = 'Migra mareas → tides (corrige typo ma_segunda_plamar)';

    protected string $legacyTable = 'mareas';
    protected string $targetModel = Tide::class;
    protected int $chunkSize = 500;

    protected function mapRow(object $row): ?array
    {
        if (empty($row->ma_fecha) || $row->ma_fecha === '0000-00-00') {
            return null;
        }

        $location = $this->toUtf8($row->ma_localidad) ?: 'El Cóndor';

        return [
            'legacy_id'          => $row->ma_id,
            'location'           => $location,
            'date'               => $row->ma_fecha,
            'first_high'         => $row->ma_primera_pleamar,
            'first_high_height'  => $this->toUtf8($row->ma_primera_pleamar_altura) ?: null,
            'first_low'          => $row->ma_primera_bajamar,
            'first_low_height'   => $this->toUtf8($row->ma_primera_bajamar_altura) ?: null,
            // typo legacy: ma_segunda_plamar (falta la 'e') → second_high
            'second_high'        => $row->ma_segunda_plamar,
            'second_high_height' => $this->toUtf8($row->ma_segunda_pleamar_altura) ?: null,
            'second_low'         => $row->ma_segunda_bajamar,
            'second_low_height'  => $this->toUtf8($row->ma_segunda_bajamar_altura) ?: null,
        ];
    }
}
