<x-mail::message>
# ¡Inscripción confirmada!

Hola {{ $registration->name }},

Recibimos tu inscripción a **{{ $event->title }}**.

@if($event->starts_at)
**Fecha:** {{ $event->starts_at->translatedFormat('l d \d\e F \d\e Y') }}@if(!$event->all_day), {{ $event->starts_at->format('H:i') }} hs@endif
@endif
@if(!empty($event->location))
**Lugar:** {{ $event->location }}
@endif

---

## Datos de tu inscripción

**Nombre:** {{ $registration->name }}
**Email:** {{ $registration->email }}
@if(!empty($registration->phone))
**Teléfono:** {{ $registration->phone }}
@endif

@if(!empty($registration->extra_data) && is_array($registration->extra_data))
@foreach($registration->extra_data as $field => $value)
@if($value !== null && $value !== '')
**{{ ucfirst(str_replace('_', ' ', (string) $field)) }}:** {{ is_bool($value) ? ($value ? 'Sí' : 'No') : $value }}
@endif
@endforeach
@endif

---

<x-mail::button :url="$eventUrl">
Ver el evento
</x-mail::button>

¡Te esperamos!<br>
{{ config('app.name') }}
</x-mail::message>
