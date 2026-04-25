<x-mail::message>
{!! $body !!}

---

*Estás recibiendo esto porque te suscribiste al newsletter de {{ config('app.name') }}.*

<x-mail::button :url="$unsubscribeUrl">
Darme de baja
</x-mail::button>
</x-mail::message>
