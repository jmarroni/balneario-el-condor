<x-admin.layouts.admin title="Encuestas"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Encuestas' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\Survey::class)
            <a href="{{ route('admin.surveys.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nueva encuesta
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Título</th>
                    <th class="text-left p-3">Pregunta</th>
                    <th class="text-left p-3">Opciones</th>
                    <th class="text-left p-3">Activa</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($surveys as $survey)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.surveys.edit', $survey) }}" class="hover:underline">
                                {{ $survey->title }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ \Illuminate\Support\Str::limit($survey->question, 60) }}</td>
                        <td class="p-3 text-slate-600">{{ count($survey->options ?? []) }}</td>
                        <td class="p-3 text-slate-600">{{ $survey->active ? 'Sí' : 'No' }}</td>
                        <td class="p-3 text-right">
                            @can('viewAny', App\Models\SurveyResponse::class)
                                <a href="{{ route('admin.surveys.responses.index', $survey) }}"
                                   class="text-slate-600 hover:underline mr-3">Respuestas</a>
                            @endcan
                            @can('delete', $survey)
                                <form method="POST" action="{{ route('admin.surveys.destroy', $survey) }}"
                                      onsubmit="return confirm('¿Eliminar encuesta?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Eliminar</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin encuestas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $surveys->links() }}</div>
</x-admin.layouts.admin>
