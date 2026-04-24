<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUsersTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        User::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/users')->assertOk()->assertSee('Usuarios');
    }

    public function test_editor_cannot_access(): void
    {
        $this->asEditor()->get('/admin/users')->assertForbidden();
    }

    public function test_moderator_cannot_access(): void
    {
        $this->asModerator()->get('/admin/users')->assertForbidden();
    }

    public function test_admin_creates_user_with_role(): void
    {
        $response = $this->asAdmin()->post('/admin/users', [
            'name'                  => 'Nuevo User',
            'email'                 => 'nuevo@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'editor',
        ]);
        $response->assertRedirect();

        $user = User::where('email', 'nuevo@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Nuevo User', $user->name);
        $this->assertTrue($user->hasRole('editor'));
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_admin_updates_user(): void
    {
        $user = User::factory()->create(['name' => 'Antes']);
        $user->assignRole('editor');

        $this->asAdmin()->put("/admin/users/{$user->id}", [
            'name'                  => 'Después',
            'email'                 => $user->email,
            'password'              => 'nuevopass123',
            'password_confirmation' => 'nuevopass123',
            'role'                  => 'moderator',
        ])->assertRedirect();

        $user->refresh();
        $this->assertSame('Después', $user->name);
        $this->assertTrue($user->hasRole('moderator'));
        $this->assertFalse($user->hasRole('editor'));
        $this->assertTrue(Hash::check('nuevopass123', $user->password));
    }

    public function test_admin_updates_user_without_changing_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('original123'),
        ]);
        $user->assignRole('editor');

        $this->asAdmin()->put("/admin/users/{$user->id}", [
            'name'                  => 'Nuevo Nombre',
            'email'                 => $user->email,
            'password'              => '',
            'password_confirmation' => '',
            'role'                  => 'editor',
        ])->assertRedirect();

        $user->refresh();
        $this->assertSame('Nuevo Nombre', $user->name);
        $this->assertTrue(Hash::check('original123', $user->password));
    }

    public function test_admin_deletes_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('editor');

        $this->asAdmin()->delete("/admin/users/{$user->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $this->asAdmin()->delete("/admin/users/{$this->admin->id}")
            ->assertRedirect()
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_store_validates_required(): void
    {
        $this->asAdmin()->post('/admin/users', [])
            ->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    public function test_store_validates_email_unique(): void
    {
        $existing = User::factory()->create(['email' => 'taken@example.com']);

        $this->asAdmin()->post('/admin/users', [
            'name'                  => 'Alguien',
            'email'                 => 'taken@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'editor',
        ])->assertSessionHasErrors(['email']);
    }

    public function test_password_confirmation_required(): void
    {
        $this->asAdmin()->post('/admin/users', [
            'name'                  => 'Alguien',
            'email'                 => 'otro@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'diferente',
            'role'                  => 'editor',
        ])->assertSessionHasErrors(['password']);
    }
}
