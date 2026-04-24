@php
    $catLabels = [
        'gourmet'   => 'Gourmet',
        'nightlife' => 'Nocturno',
    ];
    $eyebrow = $catLabels[$item->category] ?? null;
@endphp
<x-public.layouts.main :title="$item->name" :description="$item->description">
    @include('public._partials.directory-show', [
        'item'              => $item,
        'title'             => $item->name,
        'eyebrow'           => $eyebrow,
        'description'       => $item->description,
        'contact'           => [
            'phone'   => $item->phone,
            'address' => $item->address,
        ],
        'hasMap'            => true,
        'lat'               => $item->latitude,
        'lng'               => $item->longitude,
        'breadcrumb'        => ['label' => 'Gastronomía', 'route' => 'gastronomia.index'],
        'relatedHeading'    => 'Cerca de acá',
        'relatedRoute'      => 'gastronomia.index',
        'related'           => $related,
        'relatedRouteName'  => 'gastronomia.show',
        'relatedTitleField' => 'name',
    ])
</x-public.layouts.main>
