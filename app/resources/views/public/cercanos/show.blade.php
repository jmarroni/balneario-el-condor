<x-public.layouts.main :title="$item->title" :description="$item->description">
    @include('public._partials.directory-show', [
        'item'              => $item,
        'title'             => $item->title,
        'eyebrow'           => 'Lugar cercano',
        'description'       => $item->description,
        'contact'           => [
            'address' => $item->address,
        ],
        'hasMap'            => $item->latitude && $item->longitude,
        'lat'               => $item->latitude,
        'lng'               => $item->longitude,
        'breadcrumb'        => ['label' => 'Cercanos', 'route' => 'cercanos.index'],
        'relatedHeading'    => 'Más lugares',
        'relatedRoute'      => 'cercanos.index',
        'related'           => $related,
        'relatedRouteName'  => 'cercanos.show',
        'relatedTitleField' => 'title',
    ])
</x-public.layouts.main>
