<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreAdvertisingContactRequest;
use App\Models\AdvertisingContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdvertisingController extends Controller
{
    public function show(): View
    {
        return view('public.publicite.show');
    }

    public function store(StoreAdvertisingContactRequest $request): RedirectResponse
    {
        $data = $request->validated();

        AdvertisingContact::create([
            'name'      => $data['name'],
            'last_name' => $data['last_name'] ?? null,
            'email'     => $data['email'],
            'message'   => $data['message'],
            'zone'      => $data['zone'] ?? null,
            'read'      => false,
            'legacy_id' => null,
        ]);

        // TODO Plan 6: Mail::to(admin)->send(new AdvertisingReceivedMail($contact));

        return redirect()
            ->route('publicite.show')
            ->with('success', 'Recibimos tu consulta. Te contactamos pronto.');
    }
}
