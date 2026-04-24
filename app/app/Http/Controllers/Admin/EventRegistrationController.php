<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventRegistrationController extends Controller
{
    use AuthorizesRequests;

    public function index(Event $event): View
    {
        $this->authorize('viewAny', EventRegistration::class);

        $registrations = $event->registrations()
            ->latest()
            ->paginate(20);

        return view('admin.event-registrations.index', [
            'event'         => $event,
            'registrations' => $registrations,
        ]);
    }

    public function show(EventRegistration $registration): View
    {
        $this->authorize('view', $registration);

        $registration->loadMissing('event');

        return view('admin.event-registrations.show', [
            'registration' => $registration,
        ]);
    }

    public function destroy(EventRegistration $registration): RedirectResponse
    {
        $this->authorize('delete', $registration);

        $eventId = $registration->event_id;
        $registration->delete();

        return redirect()
            ->route('admin.events.registrations.index', $eventId)
            ->with('success', 'Inscripción eliminada.');
    }
}
