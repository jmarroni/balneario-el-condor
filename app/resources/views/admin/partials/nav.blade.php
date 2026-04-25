@php
    $groups = [
        'Contenido' => [
            ['news.index', 'Noticias', 'news.view'],
            ['events.index', 'Eventos', 'events.view'],
            ['pages.index', 'Páginas', 'pages.view'],
            ['recipes.index', 'Recetas', 'recipes.view'],
        ],
        'Directorio' => [
            ['lodgings.index', 'Hospedajes', 'lodgings.view'],
            ['venues.index', 'Gourmet/Nocturnos', 'venues.view'],
            ['rentals.index', 'Alquileres', 'rentals.view'],
            ['service-providers.index', 'Servicios', 'service_providers.view'],
            ['nearby-places.index', 'Cercanos', 'nearby_places.view'],
            ['useful-info.index', 'Información útil', 'useful_info.view'],
            ['tides.index', 'Mareas', 'tides.view'],
        ],
        'Comunidad' => [
            ['classifieds.index', 'Clasificados', 'classifieds.view'],
            ['gallery.index', 'Galería', 'gallery.view'],
        ],
        'Engagement' => [
            ['surveys.index', 'Encuestas', 'surveys.view'],
            ['newsletter-subscribers.index', 'Suscriptores', 'newsletter_subscribers.view'],
            ['newsletter-campaigns.index', 'Campañas', 'newsletter_campaigns.view'],
            ['contact-messages.index', 'Mensajes', 'contact_messages.view'],
            ['advertising-contacts.index', 'Publicite', 'advertising_contacts.view'],
        ],
        'Sistema' => [
            ['users.index', 'Usuarios', 'users.view'],
            ['tokens.index', 'Tokens API', '*'],
        ],
    ];
    $user = auth()->user();
@endphp
<aside class="w-60 bg-slate-800 text-slate-100 p-4 space-y-5">
    <a href="{{ route('admin.dashboard') }}" class="block text-lg font-bold">
        Balneario Admin
    </a>
    @foreach($groups as $group => $items)
        <div>
            <p class="text-xs uppercase tracking-wide text-slate-400 mb-2">{{ $group }}</p>
            <ul class="space-y-1">
                @foreach($items as [$route, $label, $permission])
                    @if($permission === '*')
                        <li>
                            <a href="{{ Route::has('admin.' . $route) ? route('admin.' . $route) : '#' }}"
                               class="block px-2 py-1 rounded hover:bg-slate-700 {{ request()->routeIs('admin.' . str_replace('.index', '', $route) . '.*') ? 'bg-slate-700' : '' }}">
                                {{ $label }}
                            </a>
                        </li>
                    @else
                        @can($permission)
                            <li>
                                <a href="{{ Route::has('admin.' . $route) ? route('admin.' . $route) : '#' }}"
                                   class="block px-2 py-1 rounded hover:bg-slate-700 {{ request()->routeIs('admin.' . str_replace('.index', '', $route) . '.*') ? 'bg-slate-700' : '' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endcan
                    @endif
                @endforeach
            </ul>
        </div>
    @endforeach
</aside>
