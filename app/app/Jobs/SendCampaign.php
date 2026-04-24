<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\NewsletterCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public NewsletterCampaign $campaign)
    {
    }

    public function handle(): void
    {
        $this->campaign->update(['status' => 'sending']);

        Log::info('SendCampaign stub', [
            'campaign_id' => $this->campaign->id,
            'subject'     => $this->campaign->subject,
        ]);

        // TODO Plan 6: integrar Resend; por ahora solo marca como sent
        $this->campaign->update([
            'status'     => 'sent',
            'sent_at'    => now(),
            'sent_count' => 0,
        ]);
    }
}
