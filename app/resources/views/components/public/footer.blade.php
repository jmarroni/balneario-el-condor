<footer class="bg-ink text-sand pt-24 pb-10 relative overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-5 lg:px-10">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 pb-14 border-b border-sand/10">
            <div>
                <img src="{{ asset('img/logo.png') }}" alt="" class="w-[86px] h-[86px] mb-5">
                <h4 class="font-display text-3xl leading-none mb-3"
                    style="font-variation-settings: 'opsz' 144, 'SOFT' 50;">
                    El faro,<br>el cóndor <em class="text-sun" style="font-variation-settings: 'opsz' 144, 'SOFT' 100;">y el mar.</em>
                </h4>
                <p class="text-sand/65 max-w-[40ch] text-[15px]">Pueblo costero a 30 km de Viedma, en la desembocadura del río Negro sobre el Atlántico patagónico. Verano, otoño, invierno y primavera.</p>
            </div>

            <div>
                <h5 class="font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">Visitar</h5>
                <ul class="flex flex-col gap-2.5">
                    <li><a href="{{ Route::has('novedades.index') ? route('novedades.index') : '#' }}" class="font-display text-base hover:text-coral-soft transition-colors" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Novedades</a></li>
                    <li><a href="{{ Route::has('eventos.index') ? route('eventos.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Eventos</a></li>
                    <li><a href="{{ Route::has('hospedajes.index') ? route('hospedajes.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Hospedajes</a></li>
                    <li><a href="{{ Route::has('gastronomia.index') ? route('gastronomia.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Gourmet</a></li>
                    <li><a href="{{ Route::has('alquileres.index') ? route('alquileres.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Alquileres</a></li>
                    <li><a href="{{ Route::has('recetas.index') ? route('recetas.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Recetas</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">Comunidad</h5>
                <ul class="flex flex-col gap-2.5">
                    <li><a href="{{ Route::has('clasificados.index') ? route('clasificados.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Clasificados</a></li>
                    <li><a href="{{ Route::has('galeria.index') ? route('galeria.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Galería</a></li>
                    <li><a href="{{ Route::has('servicios.index') ? route('servicios.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Servicios</a></li>
                    <li><a href="{{ Route::has('cercanos.index') ? route('cercanos.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Lugares cercanos</a></li>
                    <li><a href="{{ Route::has('mareas.index') ? route('mareas.index') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Tabla de mareas</a></li>
                    <li><a href="{{ Route::has('newsletter.form') ? route('newsletter.form') : '#' }}" class="font-display text-base hover:text-coral-soft" style="font-variation-settings: 'opsz' 14, 'SOFT' 30;">Newsletter</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-mono text-[11px] tracking-[0.2em] uppercase text-sun mb-4">Contacto</h5>
                <p class="font-mono text-[13px] text-sand/70 leading-[1.9]">
                    Turismo Municipal<br>
                    Av. Costanera s/n<br>
                    El Cóndor, Río Negro<br><br>
                    <strong class="text-sun">+54 9 2920 15 3300</strong><br>
                    turismo@elcondor.gob.ar
                </p>
            </div>
        </div>

        <div class="pt-7 flex flex-wrap justify-between gap-5 font-mono text-xs text-sand/55">
            <span>© {{ date('Y') }} Balneario El Cóndor · Río Negro, Patagonia Argentina</span>
            <span>Sitio público · v1</span>
        </div>
    </div>
</footer>
