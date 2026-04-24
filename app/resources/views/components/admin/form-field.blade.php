@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])
<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 mb-1">
        {{ $label }}@if($required) <span class="text-red-500">*</span>@endif
    </label>
    @if($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
                  {{ $attributes->merge(['class' => 'w-full border-slate-300 rounded']) }}>{{ old($name, $value) }}</textarea>
    @elseif($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}"
                {{ $attributes->merge(['class' => 'w-full border-slate-300 rounded']) }}>
            {{ $slot }}
        </select>
    @elseif($type === 'checkbox')
        <input id="{{ $name }}" name="{{ $name }}" type="checkbox" value="1"
               {{ old($name, $value) ? 'checked' : '' }}
               class="rounded border-slate-300">
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
               value="{{ old($name, $value) }}"
               {{ $attributes->merge(['class' => 'w-full border-slate-300 rounded']) }}>
    @endif
    @if($help)<p class="text-xs text-slate-500 mt-1">{{ $help }}</p>@endif
    @error($name)<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
