@props(['mediable'])
@php
    $mediableType = get_class($mediable);
    $items = $mediable->media()->orderBy('sort_order')->get();
@endphp
<div x-data="mediaManager({
        items: @js($items->map(fn($m) => [
            'id' => $m->id,
            'url' => asset('storage/' . $m->path),
            'alt' => $m->alt,
        ])->values()),
        mediableType: @js($mediableType),
        mediableId: {{ $mediable->id }},
        storeUrl: @js(route('admin.media.store')),
        reorderUrl: @js(route('admin.media.reorder')),
        destroyUrlTemplate: @js(route('admin.media.destroy', 'MEDIA_ID')),
        csrf: @js(csrf_token()),
    })"
    {{ $attributes->merge(['class' => 'border rounded p-4 mt-4 bg-white']) }}>
    <h3 class="font-semibold mb-3">Imágenes</h3>

    <div x-ref="grid" class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-4">
        <template x-for="item in items" :key="item.id">
            <div class="relative border rounded overflow-hidden bg-slate-50 cursor-move" :data-id="item.id">
                <img :src="item.url" :alt="item.alt || ''" class="w-full h-28 object-cover">
                <button type="button" @click="remove(item.id)"
                    class="absolute top-1 right-1 bg-red-600 text-white rounded w-6 h-6 text-xs leading-6 text-center">&times;</button>
            </div>
        </template>
        <template x-if="items.length === 0">
            <p class="text-sm text-slate-500 col-span-full">Sin imágenes aún.</p>
        </template>
    </div>

    <form @submit.prevent="upload" class="flex items-center gap-3">
        <input type="file" x-ref="file" accept="image/*" class="text-sm">
        <button type="submit" class="bg-slate-800 text-white rounded px-3 py-1 text-sm"
            :disabled="uploading">Subir</button>
        <span x-show="uploading" class="text-sm text-slate-500">Subiendo...</span>
    </form>

    <p x-show="error" class="text-red-600 text-sm mt-2" x-text="error"></p>
</div>
