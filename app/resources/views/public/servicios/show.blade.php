<x-public.layouts.main :title="$item->name" :description="$item->description">
    @include('public._partials.directory-show', [
        'item'              => $item,
        'title'             => $item->name,
        'eyebrow'           => 'Servicio',
        'description'       => $item->description,
        'contact'           => [
            'contact_name' => $item->contact_name,
            'phone'        => $item->phone,
            'email'        => $item->contact_email,
            'address'      => $item->address,
        ],
        'hasMap'            => $item->latitude && $item->longitude,
        'lat'               => $item->latitude,
        'lng'               => $item->longitude,
        'breadcrumb'        => ['label' => 'Servicios', 'route' => 'servicios.index'],
        'relatedHeading'    => 'Otros servicios',
        'relatedRoute'      => 'servicios.index',
        'related'           => $related,
        'relatedRouteName'  => 'servicios.show',
        'relatedTitleField' => 'name',
    ])
</x-public.layouts.main>
