<x-mail::message>
# Tu baja fue procesada

Lamentamos verte partir, {{ $subscriber->email }}.
Ya no vas a recibir más correos del newsletter de **{{ config('app.name') }}**.

Si te diste de baja por error, podés volver a suscribirte cuando quieras:

<x-mail::button :url="$resubscribeUrl">
Volver a suscribirme
</x-mail::button>

Gracias por haber sido parte.<br>
{{ config('app.name') }}
</x-mail::message>
