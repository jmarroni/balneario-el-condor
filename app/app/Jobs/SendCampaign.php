<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Envía una campaña de newsletter a todos los subscribers `confirmed`.
 *
 * El job se procesa en queue (Redis) por tiempo prolongado: corre `Mail::send()`
 * de forma síncrona dentro del worker para no anidar jobs y mantener el conteo
 * `sent_count` consistente. Procesa subscribers en chunks de 100 con `chunkById`.
 */
class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 30 minutos: campañas grandes pueden tardar varios minutos en SMTP serial.
     */
    public int $timeout = 1800;

    public int $tries = 3;

    /**
     * Tamaño de chunk para iterar subscribers confirmados.
     */
    private const CHUNK_SIZE = 100;

    public function __construct(public NewsletterCampaign $campaign)
    {
    }

    public function handle(): void
    {
        $this->campaign->update([
            'status'     => 'sending',
            'sent_count' => 0,
        ]);

        Log::info('SendCampaign starting', [
            'campaign_id' => $this->campaign->id,
            'subject'     => $this->campaign->subject,
        ]);

        $totalSent = 0;

        NewsletterSubscriber::query()
            ->where('status', 'confirmed')
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($subscribers) use (&$totalSent): void {
                foreach ($subscribers as $subscriber) {
                    try {
                        Mail::to($subscriber->email)
                            ->send(new NewsletterCampaignMail($this->campaign, $subscriber));
                        $totalSent++;
                    } catch (Throwable $e) {
                        Log::warning('SendCampaign subscriber send failed', [
                            'campaign_id'   => $this->campaign->id,
                            'subscriber_id' => $subscriber->id,
                            'error'         => $e->getMessage(),
                        ]);
                    }
                }

                $this->campaign->update(['sent_count' => $totalSent]);
            });

        $this->campaign->update([
            'status'     => 'sent',
            'sent_at'    => now(),
            'sent_count' => $totalSent,
        ]);

        Log::info('SendCampaign completed', [
            'campaign_id' => $this->campaign->id,
            'sent_count'  => $totalSent,
        ]);
    }

    public function failed(?Throwable $exception = null): void
    {
        Log::error('SendCampaign failed', [
            'campaign_id' => $this->campaign->id,
            'error'       => $exception?->getMessage(),
        ]);

        $this->campaign->update(['status' => 'failed']);
    }
}
