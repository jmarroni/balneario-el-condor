<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiTokenController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $tokens = $user->tokens()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.profile.tokens', compact('tokens'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ]);

        $user = $request->user();

        $abilities = $user->getPermissionNames()->toArray();
        if (empty($abilities)) {
            $abilities = ['*'];
        }

        $newToken = $user->createToken($data['name'], $abilities);

        return redirect()
            ->route('admin.tokens.index')
            ->with('success', 'Token creado.')
            ->with('new_token', $newToken->plainTextToken);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $user = $request->user();

        $token = $user->tokens()->where('id', $id)->first();

        if (! $token) {
            return redirect()
                ->route('admin.tokens.index')
                ->with('error', 'Token no encontrado.');
        }

        $token->delete();

        return redirect()
            ->route('admin.tokens.index')
            ->with('success', 'Token revocado.');
    }
}
