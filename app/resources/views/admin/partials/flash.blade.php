@php $types = ['success' => 'green', 'warning' => 'amber', 'error' => 'red', 'info' => 'blue']; @endphp
@foreach($types as $type => $color)
    @if(session($type))
        <div class="mb-4 rounded border border-{{ $color }}-200 bg-{{ $color }}-50 text-{{ $color }}-800 px-4 py-3">
            {{ session($type) }}
        </div>
    @endif
@endforeach
@if($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-4 py-3">
        <p class="font-medium">Errores:</p>
        <ul class="list-disc pl-5 text-sm mt-1">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
@endif
