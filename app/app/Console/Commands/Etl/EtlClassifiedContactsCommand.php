<?php

namespace App\Console\Commands\Etl;

use App\Models\Classified;
use App\Models\ClassifiedContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EtlClassifiedContactsCommand extends Command
{
    protected $signature   = 'etl:classified-contacts';
    protected $description = 'Migra clasificados_mail → classified_contacts';

    public function handle(): int
    {
        $processed = 0;
        $skipped   = 0;

        DB::connection('legacy')->table('clasificados_mail')
            ->orderBy('cla_id_mail')
            ->chunk(500, function ($rows) use (&$processed, &$skipped) {
                foreach ($rows as $r) {
                    $classified = Classified::where('legacy_id', $r->cla_id)->first();
                    if (! $classified) {
                        $skipped++;
                        continue;
                    }

                    ClassifiedContact::updateOrCreate(
                        ['legacy_id' => $r->cla_id_mail],
                        [
                            'classified_id'     => $classified->id,
                            'contact_name'      => (string) ($this->toUtf8($r->cla_nom_ape) ?? 'sin nombre'),
                            'contact_email'     => $this->validEmail($r->cla_correo_contacto) ?? 'sin-mail@legacy.invalid',
                            'contact_phone'     => $this->toUtf8($r->cla_telefono_contacto),
                            'message'           => $this->toUtf8($r->cla_comentario),
                            'destination_email' => $this->validEmail($r->cla_mail_dest),
                            'ip_address'        => $r->cla_ip_envio,
                            'created_at'        => $r->cla_fecha_envio ?: null,
                        ],
                    );
                    $processed++;
                }
            });

        $this->info("classified_contacts: procesadas={$processed} saltadas={$skipped}");

        return self::SUCCESS;
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

    protected function validEmail(?string $e): ?string
    {
        if (empty($e)) {
            return null;
        }
        $e = strtolower(trim($e));
        return filter_var($e, FILTER_VALIDATE_EMAIL) ? $e : null;
    }
}
