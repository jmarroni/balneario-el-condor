<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold">
                {{ config('app.name') }} · Admin
            </a>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                    ({{ auth()->user()->getRoleNames()->join(', ') }})
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>
</body>
</html>
