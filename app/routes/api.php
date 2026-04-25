<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/me', function () {
        $user = auth()->user();

        return response()->json([
            'data' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'roles'     => $user->roles->pluck('name'),
                'abilities' => $user->currentAccessToken()?->abilities ?? [],
            ],
            'meta' => [
                'version'      => 'v1',
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    })->name('me');
});
