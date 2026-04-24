{{-- Campos custom de la Fiesta de la Primavera (heredado del legacy). --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Entradas</span>
        <input type="number" min="0" name="entradas"
               value="{{ old('entradas') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink font-mono focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('entradas')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Quiero (cant.)</span>
        <input type="number" name="quiero"
               value="{{ old('quiero') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink font-mono focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('quiero')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block md:col-span-2">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Comentario</span>
        <textarea name="comentario" rows="3" maxlength="1000"
                  class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">{{ old('comentario') }}</textarea>
        @error('comentario')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>
</div>
