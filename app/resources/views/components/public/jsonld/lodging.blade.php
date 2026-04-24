@props(['lodging'])
@php
    $data = [
        '@context'    => 'https://schema.org',
        '@type'       => 'LodgingBusiness',
        'name'        => $lodging->name,
        'description' => $lodging->description,
        'url'         => route('hospedajes.show', $lodging),
        'telephone'   => $lodging->phone,
        'email'       => $lodging->email,
    ];

    if ($lodging->address) {
        $data['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $lodging->address,
            'addressLocality' => 'El Cóndor',
            'addressRegion'   => 'Río Negro',
            'addressCountry'  => 'AR',
        ];
    }

    if ($lodging->latitude !== null && $lodging->longitude !== null) {
        $data['geo'] = [
            '@type'     => 'GeoCoordinates',
            'latitude'  => (float) $lodging->latitude,
            'longitude' => (float) $lodging->longitude,
        ];
    }

    $data = array_filter($data, fn ($v) => $v !== null && $v !== '');
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
