{{-- Campos custom de la Fiesta del Tejo (heredado del legacy). --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Club / Asociación</span>
        <input type="text" name="club_asociacion" maxlength="200"
               value="{{ old('club_asociacion') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('club_asociacion')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Provincia</span>
        <input type="text" name="provincia" maxlength="100"
               value="{{ old('provincia') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('provincia')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block md:col-span-2">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Localidad</span>
        <input type="text" name="localidad" maxlength="200"
               value="{{ old('localidad') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('localidad')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Concursantes</span>
        <input type="number" min="0" name="concursantes"
               value="{{ old('concursantes') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink font-mono focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('concursantes')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Entradas</span>
        <input type="number" min="0" name="entradas"
               value="{{ old('entradas') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink font-mono focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('entradas')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Excursiones</span>
        <input type="number" min="0" name="excursiones"
               value="{{ old('excursiones') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink font-mono focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('excursiones')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="block">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Cena</span>
        <input type="number" min="0" name="cena"
               value="{{ old('cena') }}"
               class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink font-mono focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">
        @error('cena')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>

    <label class="md:col-span-2 inline-flex items-center gap-3 mt-1 cursor-pointer select-none">
        <input type="hidden" name="alojamiento" value="0">
        <input type="checkbox" name="alojamiento" value="1"
               {{ old('alojamiento') ? 'checked' : '' }}
               class="w-4 h-4 rounded border-ink-line text-coral focus:ring-coral/30">
        <span class="font-mono text-[12px] tracking-[0.1em] uppercase text-ink-soft">Necesito alojamiento</span>
    </label>

    <label class="block md:col-span-2">
        <span class="font-mono text-[10px] tracking-[0.18em] uppercase text-ink-soft block mb-1.5">Comentarios</span>
        <textarea name="comentarios" rows="3" maxlength="1000"
                  class="w-full bg-foam border border-ink-line rounded px-4 py-2.5 text-ink focus:border-coral focus:ring-2 focus:ring-coral/20 outline-none transition-colors">{{ old('comentarios') }}</textarea>
        @error('comentarios')<span class="text-coral text-xs mt-1 block">{{ $message }}</span>@enderror
    </label>
</div>
