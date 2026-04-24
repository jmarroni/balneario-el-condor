@props(['label' => 'Guardar'])
<button type="submit"
        {{ $attributes->merge(['class' => 'bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700']) }}>
    {{ $label }}
</button>
