<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('public.contacto.show');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        ContactMessage::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'subject'    => $data['subject'] ?? null,
            'message'    => $data['message'],
            'ip_address' => $request->ip(),
            'read'       => false,
        ]);

        // TODO Plan 6: Mail::to(admin)->send(new ContactReceivedMail($message));

        return redirect()
            ->route('contacto.show')
            ->with('success', 'Gracias por escribirnos. Te respondemos pronto.');
    }
}
