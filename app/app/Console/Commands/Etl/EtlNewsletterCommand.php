<?php

namespace App\Console\Commands\Etl;

use App\Models\NewsletterSubscriber;

class EtlNewsletterCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:newsletter';
    protected $description = 'Migra newsletter → newsletter_subscribers (valida mails, dedup por email)';

    protected string $legacyTable = 'newsletter';
    protected string $targetModel = NewsletterSubscriber::class;

    protected function mapRow(object $row): ?array
    {
        $email = $this->validEmail($row->nw_mail);
        if ($email === null) {
            return null;
        }

        // Dedup por email: si ya existe un subscriber con ese email pero distinto legacy_id,
        // el primer legacy_id gana y los siguientes se saltean (unique constraint en email).
        $existing = NewsletterSubscriber::where('email', $email)->first();
        if ($existing && (int) $existing->legacy_id !== (int) $row->nw_id) {
            return null;
        }

        return [
            'legacy_id'     => $row->nw_id,
            'email'         => $email,
            'status'        => 'confirmed',
            'subscribed_at' => $this->parseDate($row->nw_fecha),
            'confirmed_at'  => $this->parseDate($row->nw_fecha),
            'ip_address'    => $row->nw_ip,
        ];
    }
}
