<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class AdminApiTokensTest extends AdminTestCase
{
    public function test_admin_can_create_token(): void
    {
        $this->asAdmin()
            ->post('/admin/tokens', ['name' => 'mi-token'])
            ->assertRedirect(route('admin.tokens.index'));

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id'   => $this->admin->id,
            'tokenable_type' => User::class,
            'name'           => 'mi-token',
        ]);
    }

    public function test_admin_sees_plaintext_token_once(): void
    {
        $this->asAdmin()
            ->post('/admin/tokens', ['name' => 'token-de-prueba'])
            ->assertRedirect()
            ->assertSessionHas('new_token');

        // Después de un GET fresco, ya no aparece el token (flash limpio)
        $this->asAdmin()
            ->get('/admin/tokens')
            ->assertOk()
            ->assertSessionMissing('new_token');
    }

    public function test_admin_can_revoke_token(): void
    {
        $newToken = $this->admin->createToken('borrame');
        $tokenId  = $newToken->accessToken->id;

        $this->asAdmin()
            ->delete("/admin/tokens/{$tokenId}")
            ->assertRedirect(route('admin.tokens.index'));

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    public function test_user_cannot_revoke_another_users_token(): void
    {
        // Token del admin
        $adminToken = $this->admin->createToken('admin-token');
        $tokenId    = $adminToken->accessToken->id;

        // El editor intenta borrar el token del admin
        $this->asEditor()
            ->delete("/admin/tokens/{$tokenId}")
            ->assertRedirect()
            ->assertSessionHas('error');

        // Sigue existiendo
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    public function test_editor_can_also_manage_own_tokens(): void
    {
        $this->asEditor()
            ->get('/admin/tokens')
            ->assertOk();

        $this->asEditor()
            ->post('/admin/tokens', ['name' => 'editor-token'])
            ->assertRedirect();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id'   => $this->editor->id,
            'tokenable_type' => User::class,
            'name'           => 'editor-token',
        ]);

        $token = PersonalAccessToken::where('tokenable_id', $this->editor->id)->first();
        $this->assertNotNull($token);

        $this->asEditor()
            ->delete("/admin/tokens/{$token->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id]);
    }

    public function test_moderator_can_also_manage_own_tokens(): void
    {
        $this->asModerator()
            ->get('/admin/tokens')
            ->assertOk();

        $this->asModerator()
            ->post('/admin/tokens', ['name' => 'mod-token'])
            ->assertRedirect()
            ->assertSessionHas('new_token');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id'   => $this->moderator->id,
            'tokenable_type' => User::class,
            'name'           => 'mod-token',
        ]);
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/admin/tokens')
            ->assertRedirect(route('login'));
    }

    public function test_store_validates_name_required(): void
    {
        $this->asAdmin()
            ->post('/admin/tokens', [])
            ->assertSessionHasErrors(['name']);
    }
}
