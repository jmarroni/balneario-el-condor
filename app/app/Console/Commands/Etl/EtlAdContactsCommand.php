<?php

namespace App\Console\Commands\Etl;

use App\Models\AdvertisingContact;

class EtlAdContactsCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:ad-contacts';
    protected $description = 'Migra publicite → advertising_contacts (map pu_zona int → zone string)';

    protected string $legacyTable = 'publicite';
    protected string $targetModel = AdvertisingContact::class;

    /** Mapeo de zona legacy (int) → zona nueva (string libre en schema). */
    protected array $zoneMap = [
        1 => 'home-top',
        2 => 'sidebar',
        3 => 'footer',
    ];

    protected function mapRow(object $row): ?array
    {
        $email = $this->validEmail($row->pu_email);
        if ($email === null) {
            return null;
        }

        return [
            'legacy_id'  => $row->pu_id,
            'name'       => $this->toUtf8($row->pu_nombre) ?: 'Sin nombre',
            'last_name'  => $this->toUtf8($row->pu_apellido),
            'email'      => $email,
            'message'    => $this->toUtf8($row->pu_comentario) ?: '',
            'zone'       => $this->zoneMap[(int) $row->pu_zona] ?? 'other',
            'read'       => false,
            'created_at' => $this->parseDate($row->pu_fecha),
        ];
    }
}
