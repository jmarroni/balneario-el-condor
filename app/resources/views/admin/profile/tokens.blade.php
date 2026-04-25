<x-admin.layouts.admin title="Tokens de API">
    @if(session('new_token'))
        <div
            x-data="{ copied: false, token: @js(session('new_token')) }"
            class="mb-6 rounded border border-coral bg-coral-soft text-sand px-4 py-4"
        >
            <p class="font-semibold mb-2">Tu nuevo token de API</p>
            <p class="text-sm mb-3">
                Copialo ahora. <strong>No se volverá a mostrar.</strong>
            </p>
            <div class="flex items-center gap-2 flex-wrap">
                <code
                    class="flex-1 min-w-0 break-all bg-sand text-ink px-3 py-2 rounded font-mono text-xs"
                    x-text="token"
                ></code>
                <button
                    type="button"
                    class="px-3 py-2 rounded bg-sand text-ink text-sm hover:bg-sand-2"
                    x-on:click="navigator.clipboard.writeText(token).then(() => { copied = true; setTimeout(() => copied = false, 2000); })"
                >
                    <span x-show="!copied">Copiar al portapapeles</span>
                    <span x-show="copied" x-cloak>Copiado</span>
                </button>
            </div>
        </div>
    @endif

    <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Crear nuevo token</h2>
        <form method="POST" action="{{ route('admin.tokens.store') }}" class="flex items-end gap-3 flex-wrap">
            @csrf
            <div class="flex-1 min-w-[240px]">
                <label for="name" class="block text-sm font-medium mb-1">Nombre del token</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    maxlength="80"
                    required
                    class="w-full rounded border-slate-300"
                    placeholder="Ej: integración newsletter"
                    value="{{ old('name') }}"
                >
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 rounded bg-ink text-foam hover:bg-ink-2">
                Crear token
            </button>
        </form>
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Creado</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Último uso</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($tokens as $token)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium">{{ $token->name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $token->created_at?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nunca usado' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form
                                method="POST"
                                action="{{ route('admin.tokens.destroy', $token->id) }}"
                                onsubmit="return confirm('¿Revocar este token? La acción no se puede deshacer.');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:underline">Revocar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">
                            No tenés tokens creados todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs text-slate-500 max-w-2xl">
        Los permisos del token coinciden con los tuyos al momento de crearlo.
        Si perdés un permiso, el token seguirá con el que tenía — revocalo y creá uno nuevo.
    </p>
</x-admin.layouts.admin>
