<x-mail::message>
# Nuevo mensaje desde el formulario de contacto

Recibiste una nueva consulta en **{{ config('app.name') }}**.

**Nombre:** {{ $message->name }}
**Email:** {{ $message->email }}
@if(!empty($message->phone))
**Teléfono:** {{ $message->phone }}
@endif
@if(!empty($message->subject))
**Asunto:** {{ $message->subject }}
@endif
**IP:** {{ $message->ip_address ?? 'no disponible' }}
**Fecha:** {{ optional($message->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}

---

## Mensaje

{{ $message->message }}

---

<x-mail::button :url="url('/admin/contact-messages/' . $message->id)">
Ver en el panel
</x-mail::button>

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
