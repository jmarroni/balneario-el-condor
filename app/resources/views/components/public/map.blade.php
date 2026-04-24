@props([
    'lat',
    'lng',
    'zoom' => 15,
    'label' => 'El Cóndor',
])
<div x-data x-init="
    const map = L.map(this.$el).setView([{{ $lat }}, {{ $lng }}], {{ $zoom }});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19,
    }).addTo(map);
    L.marker([{{ $lat }}, {{ $lng }}]).addTo(map)
        .bindPopup(@js($label))
        .openPopup();
" class="h-[400px] rounded-md overflow-hidden border border-ink-line shadow-card"
   role="region"
   aria-label="Mapa de ubicación"></div>
