<x-admin.layouts.admin title="Autenticación de dos factores">
    <div class="max-w-3xl space-y-6">

        <div class="bg-white rounded shadow p-6">
            <h2 class="text-lg font-semibold mb-2">¿Por qué activar 2FA?</h2>
            <p class="text-sm text-slate-600">
                Sumá una segunda capa de seguridad a tu cuenta usando una app
                como Google Authenticator, 1Password o Authy.
                @hasrole('admin')
                    <strong class="text-red-600">Para cuentas admin es obligatorio.</strong>
                @endhasrole
            </p>
        </div>

        @if($user->two_factor_confirmed_at)
            {{-- ESTADO: 2FA ACTIVO Y CONFIRMADO --}}
            <div class="bg-white rounded shadow p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs font-medium">
                        Activado
                    </span>
                    <p class="text-sm text-slate-700">
                        Confirmado el
                        {{ \Illuminate\Support\Carbon::parse($user->two_factor_confirmed_at)->format('d/m/Y H:i') }}.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <form method="POST" action="/user/two-factor-recovery-codes">
                        @csrf
                        <button
                            type="submit"
                            class="px-4 py-2 rounded bg-slate-200 text-slate-800 text-sm hover:bg-slate-300"
                        >
                            Regenerar códigos de recuperación
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="/user/two-factor-authentication"
                        onsubmit="return confirm('¿Desactivar 2FA? Tu cuenta volverá a depender sólo de la contraseña.');"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="px-4 py-2 rounded bg-red-600 text-white text-sm hover:bg-red-700"
                        >
                            Desactivar 2FA
                        </button>
                    </form>
                </div>
            </div>

        @elseif($user->two_factor_secret)
            {{-- ESTADO: 2FA HABILITADO PERO SIN CONFIRMAR --}}
            <div class="bg-white rounded shadow p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-100 text-amber-800 text-xs font-medium">
                        Pendiente de confirmar
                    </span>
                </div>

                <p class="text-sm text-slate-700">
                    Escaneá este código QR con tu app de autenticación.
                </p>
                <div class="border rounded p-4 inline-block bg-slate-50">
                    {!! $user->twoFactorQrCodeSvg() !!}
                </div>

                <div>
                    <p class="text-sm font-medium mb-2">Códigos de recuperación</p>
                    <p class="text-xs text-slate-500 mb-2">
                        Guardalos en un lugar seguro. Te van a permitir entrar
                        si perdés acceso a tu authenticator.
                    </p>
                    <ul class="grid grid-cols-2 gap-2 text-xs font-mono bg-slate-50 p-3 rounded">
                        @foreach(json_decode(decrypt($user->two_factor_recovery_codes), true) as $code)
                            <li>{{ $code }}</li>
                        @endforeach
                    </ul>
                </div>

                <form method="POST" action="/user/confirmed-two-factor-authentication" class="space-y-3 pt-2">
                    @csrf
                    <label for="code" class="block text-sm font-medium">
                        Código del authenticator
                    </label>
                    <input
                        type="text"
                        name="code"
                        id="code"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="123 456"
                        required
                        class="rounded border-slate-300 font-mono"
                    >
                    @error('code')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <div>
                        <button
                            type="submit"
                            class="px-4 py-2 rounded bg-ink text-foam text-sm hover:bg-ink-2"
                        >
                            Confirmar y activar
                        </button>
                    </div>
                </form>
            </div>

        @else
            {{-- ESTADO: 2FA NO ACTIVADO --}}
            <div class="bg-white rounded shadow p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-200 text-slate-700 text-xs font-medium">
                        Desactivado
                    </span>
                </div>

                <p class="text-sm text-slate-700">
                    Activá 2FA para protegerte ante una contraseña filtrada.
                </p>

                <form method="POST" action="/user/two-factor-authentication">
                    @csrf
                    <button
                        type="submit"
                        class="px-4 py-2 rounded bg-ink text-foam text-sm hover:bg-ink-2"
                    >
                        Activar 2FA
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-admin.layouts.admin>
