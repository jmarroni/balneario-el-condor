<x-mail::message>
# Tenés una consulta sobre tu clasificado

Hola {{ $classified->contact_name }},

Alguien dejó un mensaje sobre tu aviso **"{{ $classified->title }}"** en {{ config('app.name') }}.

**De:** {{ $contact->contact_name }}
**Email:** {{ $contact->contact_email }}
@if(!empty($contact->contact_phone))
**Teléfono:** {{ $contact->contact_phone }}
@endif

---

## Mensaje

{{ $contact->message }}

---

Para responder, contestá directamente a este mail o escribile a {{ $contact->contact_email }}.

<x-mail::button :url="$classifiedUrl">
Ver mi clasificado
</x-mail::button>

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
