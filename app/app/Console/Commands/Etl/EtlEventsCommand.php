<?php

namespace App\Console\Commands\Etl;

use App\Models\Event;
use Illuminate\Support\Str;

class EtlEventsCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:events';
    protected $description = 'Migra agenda → events + crea eventos seed para Tejo y Primavera';

    protected string $legacyTable = 'agenda';
    protected string $targetModel = Event::class;

    protected function mapRow(object $row): ?array
    {
        $title = $this->toUtf8($row->ag_titulo);
        if (empty($title)) return null;

        return [
            'legacy_id'              => $row->ag_id,
            'title'                  => $title,
            'slug'                   => Str::slug($this->toUtf8($row->ag_url_amigable) ?: $title) ?: 'evento-' . $row->ag_id,
            'description'            => $this->toUtf8($row->ag_descripcion_corta),
            'location'               => $this->toUtf8($row->ag_lugar),
            'external_url'           => $this->toUtf8($row->ag_link) ?: null,
            'starts_at'              => $this->parseDate($row->ag_fecha),
            'all_day'                => (bool) $row->ag_todo_dias,
            'featured'               => (bool) $row->ag_destacado,
            'sort_order'             => (int) ($row->ag_orden ?? 0),
            'accepts_registrations'  => false,
        ];
    }

    public function handle(): int
    {
        parent::handle();

        // Seed de eventos marco para tejofiesta y primavera
        Event::updateOrCreate(
            ['slug' => 'fiesta-del-tejo'],
            [
                'title'                 => 'Fiesta del Tejo',
                'description'           => 'Evento tradicional migrado desde legacy.',
                'accepts_registrations' => true,
                'featured'              => false,
            ],
        );
        Event::updateOrCreate(
            ['slug' => 'fiesta-de-la-primavera'],
            [
                'title'                 => 'Fiesta de la Primavera',
                'description'           => 'Evento tradicional migrado desde legacy.',
                'accepts_registrations' => true,
                'featured'              => false,
            ],
        );

        return self::SUCCESS;
    }
}
