<?php

namespace App\Console\Commands\Etl;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EtlEventRegsCommand extends Command
{
    protected $signature   = 'etl:event-regs';
    protected $description = 'Migra tejofiesta + primavera → event_registrations';

    public function handle(): int
    {
        $tejo      = Event::where('slug', 'fiesta-del-tejo')->firstOrFail();
        $primavera = Event::where('slug', 'fiesta-de-la-primavera')->firstOrFail();

        $this->migrateTejo($tejo);
        $this->migratePrimavera($primavera);

        return self::SUCCESS;
    }

    protected function migrateTejo(Event $event): void
    {
        DB::connection('legacy')->table('tejofiesta')->orderBy('te_id')->chunk(500, function ($rows) use ($event) {
            foreach ($rows as $r) {
                $email = $this->validEmail($r->te_mail);
                EventRegistration::updateOrCreate(
                    ['event_id' => $event->id, 'legacy_id' => 'tejo-' . $r->te_id],
                    [
                        'name'       => $this->toUtf8($r->te_nombre . ' ' . $r->te_apellido),
                        'email'      => $email ?? 'sin-mail+' . $r->te_id . '@legacy.invalid',
                        'phone'      => $this->toUtf8($r->te_telefono),
                        'extra_data' => [
                            'club'         => $this->toUtf8($r->te_club_asociacion),
                            'provincia'    => $this->toUtf8($r->te_provincia),
                            'localidad'    => $this->toUtf8($r->te_localidad),
                            'alojamiento'  => (bool) $r->te_alojamiento,
                            'asistencia'   => $this->toUtf8($r->te_asistencia),
                            'concursantes' => (int) $r->te_concursantes,
                            'entradas'     => (int) $r->te_entradas,
                            'excursiones'  => (int) $r->te_excursiones,
                            'cena'         => (int) $r->te_cena,
                            'comentarios'  => $this->toUtf8($r->te_comentarios),
                        ],
                    ],
                );
            }
        });
    }

    protected function migratePrimavera(Event $event): void
    {
        DB::connection('legacy')->table('primavera')->orderBy('pri_id')->chunk(500, function ($rows) use ($event) {
            foreach ($rows as $r) {
                $email = $this->validEmail($r->pri_mail);
                EventRegistration::updateOrCreate(
                    ['event_id' => $event->id, 'legacy_id' => 'primavera-' . $r->pri_id],
                    [
                        'name'       => $this->toUtf8($r->pri_nombre . ' ' . $r->pri_apellido),
                        'email'      => $email ?? 'sin-mail+' . $r->pri_id . '@legacy.invalid',
                        'phone'      => $this->toUtf8($r->pri_telefono),
                        'extra_data' => [
                            'entradas'   => (int) $r->pri_entradas,
                            'comentario' => $this->toUtf8($r->pri_comentario),
                            'quiero'     => (int) $r->pri_quiero,
                            'ip'         => $r->pri_ip,
                        ],
                    ],
                );
            }
        });
    }

    protected function toUtf8(?string $value): ?string
    {
        if ($value === null || $value === '') return $value;
        if (mb_check_encoding($value, 'UTF-8') && ! preg_match('/[\xC2-\xDF][\x80-\xBF]/', $value)) return $value;
        return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
    }

    protected function validEmail(?string $email): ?string
    {
        if (empty($email)) return null;
        $email = strtolower(trim($email));
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }
}
