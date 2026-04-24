<x-admin.layouts.admin title="Importar mareas desde CSV"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Mareas' => route('admin.tides.index'),
        'Importar' => null,
    ]">
    <div class="bg-white rounded shadow p-6 max-w-3xl">
        <p class="text-sm text-slate-600 mb-4">
            Subí un archivo CSV con las columnas:
            <code class="text-xs bg-slate-100 px-1 rounded">date, first_high, first_high_height, first_low, first_low_height, second_high, second_high_height, second_low, second_low_height</code>.
            Las filas con fecha existente se actualizan.
        </p>

        <form method="POST" action="{{ route('admin.tides.import') }}" enctype="multipart/form-data">
            @csrf

            <x-admin.form-field name="location" label="Ubicación" :value="old('location', 'El Cóndor')" />

            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-slate-700 mb-1">
                    Archivo CSV <span class="text-red-500">*</span>
                </label>
                <input id="file" type="file" name="file" accept=".csv,.txt" required
                    class="block w-full text-sm text-slate-700">
                @error('file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-admin.submit-button label="Importar" />
        </form>

        @if(session('import_errors'))
            <div class="mt-6 bg-red-50 border border-red-200 rounded p-4 text-sm">
                <p class="font-semibold text-red-700 mb-2">Errores de importación:</p>
                <ul class="list-disc pl-5 text-red-700">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-admin.layouts.admin>
