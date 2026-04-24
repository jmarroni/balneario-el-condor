@props(['title' => 'Admin', 'breadcrumbs' => null])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin' }} — Balneario El Cóndor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen flex">
    @include('admin.partials.nav')

    <div class="flex-1 flex flex-col">
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <div>
                @if($breadcrumbs)
                    <x-admin.breadcrumbs :items="$breadcrumbs" />
                @endif
                <h1 class="text-xl font-semibold">{{ $title ?? 'Admin' }}</h1>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-slate-600">{{ auth()->user()->name }}</span>
                <span class="text-xs bg-slate-200 rounded px-2 py-1">{{ auth()->user()->roles->pluck('name')->join(', ') }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-red-600 hover:underline">Salir</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6">
            @include('admin.partials.flash')
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
