<x-mail::message>
# Nueva consulta de publicidad

Llegó una nueva consulta desde el formulario "Publicite con nosotros" de **{{ config('app.name') }}**.

**Nombre:** {{ $ad->name }}@if(!empty($ad->last_name)) {{ $ad->last_name }}@endif
**Email:** {{ $ad->email }}
@if(!empty($ad->zone))
**Zona de interés:** {{ $ad->zone }}
@endif
**Fecha:** {{ optional($ad->created_at)->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}

---

## Mensaje

{{ $ad->message }}

---

<x-mail::button :url="url('/admin/advertising-contacts/' . $ad->id)">
Ver en el panel
</x-mail::button>

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
