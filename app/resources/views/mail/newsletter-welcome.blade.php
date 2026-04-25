<x-mail::message>
# ¡Suscripción confirmada!

Gracias por sumarte al newsletter de **{{ config('app.name') }}**.
A partir de ahora vas a recibir las novedades del balneario, agenda de eventos y noticias importantes.

Si en algún momento querés dejar de recibir nuestros mails, podés darte de baja con un click:

<x-mail::button :url="$unsubscribeUrl">
Darme de baja
</x-mail::button>

¡Nos vemos en El Cóndor!<br>
{{ config('app.name') }}
</x-mail::message>
