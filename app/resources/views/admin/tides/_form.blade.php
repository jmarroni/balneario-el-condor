@php
    $formatTime = fn ($v) => $v ? substr((string) $v, 0, 5) : null;
@endphp

<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="location" label="Ubicación" :value="$tide->location ?? 'El Cóndor'" />
    <x-admin.form-field name="date" label="Fecha" type="date"
        :value="optional($tide->date)->format('Y-m-d')" required />
</div>

<h3 class="font-semibold mt-6 mb-2 text-slate-700">1ª Pleamar / Bajamar</h3>
<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="first_high" label="Pleamar" type="time"
        :value="$formatTime($tide->first_high)" />
    <x-admin.form-field name="first_high_height" label="Altura pleamar" :value="$tide->first_high_height" help='Ej: "3.20 m"' />
    <x-admin.form-field name="first_low" label="Bajamar" type="time"
        :value="$formatTime($tide->first_low)" />
    <x-admin.form-field name="first_low_height" label="Altura bajamar" :value="$tide->first_low_height" />
</div>

<h3 class="font-semibold mt-6 mb-2 text-slate-700">2ª Pleamar / Bajamar</h3>
<div class="grid grid-cols-2 gap-4">
    <x-admin.form-field name="second_high" label="Pleamar" type="time"
        :value="$formatTime($tide->second_high)" />
    <x-admin.form-field name="second_high_height" label="Altura pleamar" :value="$tide->second_high_height" />
    <x-admin.form-field name="second_low" label="Bajamar" type="time"
        :value="$formatTime($tide->second_low)" />
    <x-admin.form-field name="second_low_height" label="Altura bajamar" :value="$tide->second_low_height" />
</div>
