@php
    $organization = [
        '@context' => 'https://schema.org',
        '@type'    => 'TouristDestination',
        'name'     => 'Balneario El Cóndor',
        'description' => 'Pueblo costero a 30 km de Viedma, en la desembocadura del río Negro sobre el Atlántico patagónico.',
        'url'      => route('home'),
        'address'  => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'El Cóndor',
            'addressRegion'   => 'Río Negro',
            'addressCountry'  => 'AR',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => -41.05,
            'longitude' => -62.82,
        ],
    ];

    $website = [
        '@context'      => 'https://schema.org',
        '@type'         => 'WebSite',
        'name'          => 'Balneario El Cóndor',
        'url'           => route('home'),
        'inLanguage'    => 'es-AR',
        'publisher'     => [
            '@type' => 'Organization',
            'name'  => 'Turismo Balneario El Cóndor',
            'url'   => route('home'),
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode($website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
