<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classified;
use App\Models\ClassifiedContact;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassifiedContactController extends Controller
{
    use AuthorizesRequests;

    public function index(Classified $classified): View
    {
        $this->authorize('viewAny', ClassifiedContact::class);

        $contacts = $classified->contacts()
            ->latest()
            ->paginate(20);

        return view('admin.classified-contacts.index', [
            'classified' => $classified,
            'contacts' => $contacts,
        ]);
    }

    public function show(ClassifiedContact $contact): View
    {
        $this->authorize('view', $contact);

        $contact->loadMissing('classified');

        return view('admin.classified-contacts.show', [
            'contact' => $contact,
        ]);
    }

    public function destroy(ClassifiedContact $contact): RedirectResponse
    {
        $this->authorize('delete', $contact);

        $classifiedId = $contact->classified_id;
        $contact->delete();

        return redirect()
            ->route('admin.classifieds.contacts.index', $classifiedId)
            ->with('success', 'Contacto eliminado.');
    }
}
