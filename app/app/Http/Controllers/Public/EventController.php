<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Mail\EventRegistrationConfirmationMail;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Reglas extra por slug heredadas del legacy. Cada slug tiene su propio
     * conjunto de campos custom que se persisten en `extra_data` (JSON).
     *
     * @var array<string, array<string, array<int, string>>>
     */
    private const SLUG_RULES = [
        'fiesta-del-tejo' => [
            'club_asociacion' => ['nullable', 'string', 'max:200'],
            'provincia'       => ['nullable', 'string', 'max:100'],
            'localidad'       => ['nullable', 'string', 'max:200'],
            'alojamiento'     => ['nullable', 'boolean'],
            'concursantes'    => ['nullable', 'integer', 'min:0'],
            'entradas'        => ['nullable', 'integer', 'min:0'],
            'excursiones'     => ['nullable', 'integer', 'min:0'],
            'cena'            => ['nullable', 'integer', 'min:0'],
            'comentarios'     => ['nullable', 'string', 'max:1000'],
        ],
        'fiesta-de-la-primavera' => [
            'entradas'   => ['nullable', 'integer', 'min:0'],
            'comentario' => ['nullable', 'string', 'max:1000'],
            'quiero'     => ['nullable', 'integer'],
        ],
    ];

    /**
     * Listado público de eventos. `cuando=proximos` (default) muestra futuros y
     * eventos sin fecha que aceptan inscripciones; `cuando=pasados` solo finalizados.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('cuando') === 'pasados' ? 'pasados' : 'proximos';

        $base = Event::query()->with('media');

        if ($filter === 'pasados') {
            $base->whereNotNull('starts_at')
                ->where('starts_at', '<', now())
                ->orderByDesc('starts_at');
        } else {
            $base->where(function ($q) {
                $q->where('starts_at', '>=', now())
                    ->orWhereNull('starts_at')
                    ->orWhere('accepts_registrations', true);
            })->orderByRaw('starts_at IS NULL, starts_at ASC');
        }

        $events = $base->paginate(12)->withQueryString();

        $featured = $filter === 'proximos'
            ? Event::query()
                ->where('featured', true)
                ->where(function ($q) {
                    $q->where('starts_at', '>=', now())
                        ->orWhereNull('starts_at');
                })
                ->orderBy('starts_at')
                ->with('media')
                ->first()
            : null;

        return view('public.eventos.index', [
            'events'   => $events,
            'featured' => $featured,
            'filter'   => $filter,
        ]);
    }

    /**
     * Detalle de un evento. El slug se resuelve por route-model binding.
     */
    public function show(Event $event): View
    {
        $event->load('media');

        return view('public.eventos.show', [
            'event' => $event,
        ]);
    }

    /**
     * POST /eventos/{event:slug}/inscripcion
     *
     * Reglas base + reglas custom según slug. Los campos extra se guardan
     * dentro de `extra_data` (JSON) — la columna `name`/`email`/`phone` queda
     * canónica para el listado del admin.
     */
    public function register(Request $request, Event $event): RedirectResponse
    {
        abort_unless($event->accepts_registrations, 403, 'Evento cerrado a inscripciones');

        $rules = [
            'name'  => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:200'],
            'phone' => ['nullable', 'string', 'max:100'],
        ];

        if (isset(self::SLUG_RULES[$event->slug])) {
            $rules += self::SLUG_RULES[$event->slug];
        }

        $data = $request->validate($rules);

        $extra = collect($data)
            ->except(['name', 'email', 'phone'])
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->toArray();

        $registration = EventRegistration::create([
            'event_id'   => $event->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'extra_data' => $extra ?: null,
            'ip_address' => $request->ip(),
            'legacy_id'  => 'form-'.Str::random(10),
        ]);

        Mail::to($data['email'])
            ->queue(new EventRegistrationConfirmationMail($event, $registration));

        return redirect()
            ->route('eventos.show', $event)
            ->with('success', '¡Inscripción recibida! Te contactamos por mail.');
    }
}
