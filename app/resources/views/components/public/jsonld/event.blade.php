@props(['event'])
@php
    $data = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Event',
        'name'        => $event->title,
        'description' => $event->description,
        'url'         => route('eventos.show', $event),
        'startDate'   => optional($event->starts_at)?->toIso8601String(),
        'endDate'     => optional($event->ends_at)?->toIso8601String(),
        'eventStatus' => 'https://schema.org/EventScheduled',
        'location'    => [
            '@type'   => 'Place',
            'name'    => $event->location ?: 'Balneario El Cóndor',
            'address' => [
                '@type'           => 'PostalAddress',
                'addressLocality' => 'El Cóndor',
                'addressRegion'   => 'Río Negro',
                'addressCountry'  => 'AR',
            ],
        ],
        'organizer'   => [
            '@type' => 'Organization',
            'name'  => 'Turismo Balneario El Cóndor',
            'url'   => route('home'),
        ],
    ];
    $data = array_filter($data, fn ($v) => $v !== null && $v !== '');
@endphp
<script type="application/ld+json">
{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
