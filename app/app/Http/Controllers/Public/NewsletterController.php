<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\SubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsletterController extends Controller
{
    public function show(): View
    {
        return view('public.newsletter.show');
    }

    /**
     * Crea o reactiva una suscripción en estado pending y dispara el
     * email de confirmación (Plan 6). Doble opt-in: la suscripción no
     * se da por confirmada hasta que el usuario abre el link recibido.
     */
    public function subscribe(SubscribeRequest $request): RedirectResponse
    {
        $email = strtolower(trim((string) $request->validated('email')));
        $token = Str::random(48);

        $sub = NewsletterSubscriber::updateOrCreate(
            ['email' => $email],
            [
                'status'             => 'pending',
                'confirmation_token' => $token,
                'subscribed_at'      => now(),
                'ip_address'         => $request->ip(),
            ]
        );

        // TODO Plan 6: Mail::to($sub->email)->send(new NewsletterConfirmMail($sub));
        Log::info('newsletter.confirm.pending', [
            'email' => $sub->email,
            'url'   => route('newsletter.confirm', $token),
        ]);

        return redirect()
            ->route('newsletter.form')
            ->with('success', 'Te enviamos un mail para que confirmes tu suscripción.');
    }

    public function confirm(string $token): View
    {
        $sub = NewsletterSubscriber::where('confirmation_token', $token)->firstOrFail();

        if ($sub->status === 'pending') {
            $sub->update([
                'status'       => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }

        return view('public.newsletter.confirmed', [
            'sub' => $sub,
        ]);
    }

    public function unsubscribe(string $token): View
    {
        $sub = NewsletterSubscriber::where('confirmation_token', $token)->firstOrFail();

        $sub->update([
            'status'           => 'unsubscribed',
            'unsubscribed_at'  => now(),
        ]);

        return view('public.newsletter.unsubscribed', [
            'sub' => $sub,
        ]);
    }
}
