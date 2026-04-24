<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvertisingContact;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdvertisingContactController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', AdvertisingContact::class);

        $contacts = AdvertisingContact::query()
            ->latest()
            ->paginate(25);

        return view('admin.advertising-contacts.index', compact('contacts'));
    }

    public function show(AdvertisingContact $adContact): View
    {
        $this->authorize('view', $adContact);

        return view('admin.advertising-contacts.show', [
            'adContact' => $adContact,
        ]);
    }

    public function destroy(AdvertisingContact $adContact): RedirectResponse
    {
        $this->authorize('delete', $adContact);

        $adContact->delete();

        return redirect()
            ->route('admin.advertising-contacts.index')
            ->with('success', 'Contacto eliminado.');
    }
}
