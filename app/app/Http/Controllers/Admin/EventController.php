<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', Event::class);

        $events = Event::query()
            ->orderByDesc('starts_at')
            ->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $this->authorize('create', Event::class);

        return view('admin.events.create', [
            'event' => new Event(),
        ]);
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title']);
        $data['all_day']               = (bool) ($data['all_day'] ?? false);
        $data['featured']              = (bool) ($data['featured'] ?? false);
        $data['accepts_registrations'] = (bool) ($data['accepts_registrations'] ?? false);

        $event = Event::create($data);

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('success', 'Evento creado.');
    }

    public function show(Event $event): RedirectResponse
    {
        $this->authorize('view', $event);

        return redirect()->route('admin.events.edit', $event);
    }

    public function edit(Event $event): View
    {
        $this->authorize('update', $event);

        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        $data['all_day']               = (bool) ($data['all_day'] ?? false);
        $data['featured']              = (bool) ($data['featured'] ?? false);
        $data['accepts_registrations'] = (bool) ($data['accepts_registrations'] ?? false);

        $event->update($data);

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('success', 'Evento actualizado.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Evento eliminado.');
    }
}
