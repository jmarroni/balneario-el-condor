<x-admin.layouts.admin title="Usuarios"
    :breadcrumbs="[
        'Admin' => route('admin.dashboard'),
        'Usuarios' => null,
    ]">
    <div class="flex justify-end mb-4">
        @can('create', App\Models\User::class)
            <a href="{{ route('admin.users.create') }}"
               class="bg-slate-800 text-white rounded px-4 py-2 hover:bg-slate-700">
                Nuevo usuario
            </a>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600 uppercase text-xs">
                <tr>
                    <th class="text-left p-3">Nombre</th>
                    <th class="text-left p-3">Email</th>
                    <th class="text-left p-3">Roles</th>
                    <th class="text-right p-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($users as $u)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.users.edit', $u) }}" class="hover:underline">
                                {{ $u->name }}
                            </a>
                        </td>
                        <td class="p-3 text-slate-600">{{ $u->email }}</td>
                        <td class="p-3">
                            @foreach($u->roles as $role)
                                <span class="inline-block text-xs bg-slate-200 text-slate-700 rounded px-2 py-1 mr-1">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="p-3 text-right">
                            @if($u->id !== auth()->id())
                                @can('delete', $u)
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                          onsubmit="return confirm('¿Eliminar usuario?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                @endcan
                            @else
                                <span class="text-xs text-slate-400">(vos)</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin usuarios.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</x-admin.layouts.admin>
