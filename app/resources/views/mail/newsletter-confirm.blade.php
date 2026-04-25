<x-mail::message>
# ¡Hola!

Recibimos tu pedido de suscripción al newsletter de **{{ config('app.name') }}**.

Para confirmar tu dirección {{ $subscriber->email }} hacé click en el botón:

<x-mail::button :url="$confirmUrl">
Confirmar suscripción
</x-mail::button>

Si no fuiste vos, podés ignorar este mensaje y tu dirección no será agregada.

Saludos,<br>
{{ config('app.name') }}
</x-mail::message>
