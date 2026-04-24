<x-public.layouts.main :title="$item->title" :description="$item->description">
    @include('public._partials.directory-show', [
        'item'              => $item,
        'title'             => $item->title,
        'eyebrow'           => 'Capacidad · ' . $item->places . ' ' . ($item->places == 1 ? 'persona' : 'personas'),
        'description'       => $item->description,
        'contact'           => [
            'contact_name' => $item->contact_name,
            'phone'        => $item->phone,
            'email'        => $item->email,
            'address'      => $item->address,
            'places'       => $item->places,
        ],
        'hasMap'            => false, // Rentals no tienen lat/lng
        'lat'               => null,
        'lng'               => null,
        'breadcrumb'        => ['label' => 'Alquileres', 'route' => 'alquileres.index'],
        'relatedHeading'    => 'Otros alquileres',
        'relatedRoute'      => 'alquileres.index',
        'related'           => $related,
        'relatedRouteName'  => 'alquileres.show',
        'relatedTitleField' => 'title',
    ])
</x-public.layouts.main>
