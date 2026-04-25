<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    private function admin(bool $confirmed = false): User
    {
        $user = User::factory()->create([
            'two_factor_confirmed_at' => $confirmed ? now() : null,
        ]);
        $user->assignRole('admin');

        return $user;
    }

    private function editor(): User
    {
        $user = User::factory()->create();
        $user->assignRole('editor');

        return $user;
    }

    /**
     * Marca la sesión como con password confirmada para saltear el middleware
     * RequirePassword que Fortify aplica con confirmPassword: true.
     *
     * @return array<string, int>
     */
    private function passwordConfirmedSession(): array
    {
        return ['auth.password_confirmed_at' => time()];
    }

    public function test_user_can_view_two_factor_page(): void
    {
        $user = $this->admin(confirmed: true);

        $this->actingAs($user)
            ->get('/admin/two-factor')
            ->assertOk()
            ->assertSee('dos factores', false);
    }

    public function test_user_can_enable_two_factor(): void
    {
        $user = $this->editor();

        $this->actingAs($user)
            ->withSession($this->passwordConfirmedSession())
            ->post('/user/two-factor-authentication')
            ->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_user_can_confirm_two_factor(): void
    {
        $user = $this->editor();

        $this->actingAs($user)
            ->withSession($this->passwordConfirmedSession())
            ->post('/user/two-factor-authentication')
            ->assertRedirect();
        $user->refresh();

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
        $code = $google2fa->getCurrentOtp($secret);

        $this->actingAs($user)
            ->withSession($this->passwordConfirmedSession())
            ->post('/user/confirmed-two-factor-authentication', ['code' => $code])
            ->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function test_user_can_disable_two_factor(): void
    {
        $user = $this->admin(confirmed: true);
        $this->actingAs($user)
            ->withSession($this->passwordConfirmedSession())
            ->post('/user/two-factor-authentication')
            ->assertRedirect();

        $this->actingAs($user)
            ->withSession($this->passwordConfirmedSession())
            ->delete('/user/two-factor-authentication')
            ->assertRedirect();

        $user->refresh();
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_recovery_codes_are_generated_and_unique(): void
    {
        $user = $this->editor();

        $this->actingAs($user)
            ->withSession($this->passwordConfirmedSession())
            ->post('/user/two-factor-authentication')
            ->assertRedirect();
        $user->refresh();

        $this->assertNotNull($user->two_factor_recovery_codes, 'Recovery codes should be set');

        $codes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        $this->assertIsArray($codes);
        $this->assertCount(8, $codes);
        $this->assertCount(8, array_unique($codes), 'Recovery codes must be unique');
    }

    public function test_admin_without_2fa_is_redirected_to_setup(): void
    {
        $user = $this->admin(confirmed: false);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('admin.two-factor.show'));
    }

    public function test_editor_without_2fa_can_access_dashboard(): void
    {
        $user = $this->editor();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_admin_with_2fa_confirmed_can_access_dashboard(): void
    {
        $user = $this->admin(confirmed: true);

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }
}
