<x-admin.layouts.admin title="Galería"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Galería' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\GalleryImage::class)
            <a href="{{ route('admin.gallery.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva imagen
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Miniatura</th>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Tomada</th>
                    <th class="text-right p-3">Vistas</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($images as $image)
                    <tr>
                        <td class="p-3">
                            @if($image->thumb_path)
                                <img src="{{ asset('storage/' . $image->thumb_path) }}"
                                     alt="{{ $image->title }}"
                                     class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-slate-100 rounded"></div>
                            @endif
                        </td>
                        <td class="p-3">
                            <a href="{{ route('admin.gallery.edit', $image) }}" class="hover:underline">
                                {{ $image->title ?? '—' }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $image->taken_on?->format('d/m/Y') ?? '—' }}</td>
                        <td class="p-3 text-right text-slate-600">{{ $image->views }}</td>
                        <td class="p-3 text-right">
                            @can('delete', $image)
                                <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}"
                                      onsubmit="return confirm('¿Eliminar imagen?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin imágenes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $images->links() }}</div>
</x-admin.layouts.admin>
