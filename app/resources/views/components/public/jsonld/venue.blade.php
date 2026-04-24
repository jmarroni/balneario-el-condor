@props(['venue'])
@php
    $type = $venue->category === 'nightlife' ? 'NightClub' : 'Restaurant';

    $data = [
        '@context'    => 'https://schema.org',
        '@type'       => $type,
        'name'        => $venue->name,
        'description' => $venue->description,
        'url'         => route('gastronomia.show', $venue),
        'telephone'   => $venue->phone,
    ];

    if ($venue->address) {
        $data['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $venue->address,
            'addressLocality' => 'El Cóndor',
            'addressRegion'   => 'Río Negro',
            'addressCountry'  => 'AR',
        ];
    }

    if ($venue->latitude !== null && $venue->longitude !== null) {
        $data['geo'] = [
            '@type'     => 'GeoCoordinates',
            'latitude'  => (float) $venue->latitude,
            'longitude' => (float) $venue->longitude,
        ];
    }

    $data = array_filter($data, fn ($v) => $v !== null && $v !== '');
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
