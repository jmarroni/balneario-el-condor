<x-admin.form-field name="title" label="Título" :value="$survey->title" required />
<x-admin.form-field name="question" label="Pregunta" type="textarea" :value="$survey->question" required />

<div class="mb-4">
    <label class="block text-sm font-medium text-slate-700 mb-2">
        Opciones <span class="text-red-500">*</span>
    </label>
    <div x-data="{ options: @js(old('options', $survey->options ?? [['key' => 1, 'label' => '']])) }">
        <template x-for="(opt, i) in options" :key="i">
            <div class="flex gap-2 mb-2">
                <input type="number" :name="`options[${i}][key]`" x-model.number="opt.key"
                       class="w-24 border-slate-300 rounded" placeholder="Key">
                <input type="text" :name="`options[${i}][label]`" x-model="opt.label"
                       class="flex-1 border-slate-300 rounded" placeholder="Etiqueta">
                <button type="button" @click="options.splice(i, 1)"
                        class="text-red-600 px-2">&times;</button>
            </div>
        </template>
        <button type="button" @click="options.push({key: options.length + 1, label: ''})"
                class="text-sm text-slate-600 hover:underline">+ Agregar opción</button>
    </div>
    @error('options')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    @foreach(['options.0.key', 'options.0.label'] as $field)
        @error($field)<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    @endforeach
</div>

<x-admin.form-field name="active" label="Activa" type="checkbox" :value="$survey->active ?? true" />
