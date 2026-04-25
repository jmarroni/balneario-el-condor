<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\SubscribeRequest;
use App\Mail\NewsletterConfirmMail;
use App\Mail\NewsletterUnsubscribedMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
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
     * email de confirmación (double opt-in). La suscripción no
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

        Mail::to($sub->email)->queue(new NewsletterConfirmMail($sub));

        return redirect()
            ->route('newsletter.form')
            ->with('success', 'Te enviamos un mail para que confirmes tu suscripción.');
    }

    public function confirm(string $token): View
    {
        $sub = NewsletterSubscriber::where('confirmation_token', $token)->firstOrFail();

        $justConfirmed = false;

        if ($sub->status === 'pending') {
            $sub->update([
                'status'       => 'confirmed',
                'confirmed_at' => now(),
            ]);
            $justConfirmed = true;
        }

        if ($justConfirmed) {
            Mail::to($sub->email)->queue(new NewsletterWelcomeMail($sub));
        }

        return view('public.newsletter.confirmed', [
            'sub' => $sub,
        ]);
    }

    public function unsubscribe(string $token): View
    {
        $sub = NewsletterSubscriber::where('confirmation_token', $token)->firstOrFail();

        $wasActive = $sub->status !== 'unsubscribed';

        $sub->update([
            'status'           => 'unsubscribed',
            'unsubscribed_at'  => now(),
        ]);

        if ($wasActive) {
            Mail::to($sub->email)->queue(new NewsletterUnsubscribedMail($sub));
        }

        return view('public.newsletter.unsubscribed', [
            'sub' => $sub,
        ]);
    }
}
