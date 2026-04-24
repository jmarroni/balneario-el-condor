@php
    $typeLabels = [
        'hotel'   => 'Hotel',
        'casa'    => 'Casa',
        'camping' => 'Camping',
        'hostel'  => 'Hostel',
        'other'   => 'Hospedaje',
    ];
    $eyebrow = $typeLabels[$item->type] ?? 'Hospedaje';
@endphp
<x-public.layouts.main :title="$item->name" :description="$item->description">
    <x-slot:head>
        <x-public.jsonld.lodging :lodging="$item" />
    </x-slot:head>
    @include('public._partials.directory-show', [
        'item'              => $item,
        'title'             => $item->name,
        'eyebrow'           => $eyebrow,
        'description'       => $item->description,
        'contact'           => [
            'phone'   => $item->phone,
            'email'   => $item->email,
            'website' => $item->website,
            'address' => $item->address,
        ],
        'hasMap'            => true,
        'lat'               => $item->latitude,
        'lng'               => $item->longitude,
        'breadcrumb'        => ['label' => 'Hospedajes', 'route' => 'hospedajes.index'],
        'relatedHeading'    => 'Más hospedajes',
        'relatedRoute'      => 'hospedajes.index',
        'related'           => $related,
        'relatedRouteName'  => 'hospedajes.show',
        'relatedTitleField' => 'name',
    ])
</x-public.layouts.main>
