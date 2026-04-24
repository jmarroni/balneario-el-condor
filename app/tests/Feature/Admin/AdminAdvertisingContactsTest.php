<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\AdvertisingContact;

class AdminAdvertisingContactsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        AdvertisingContact::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/advertising-contacts')->assertOk()->assertSee('Publicite');
    }

    public function test_admin_sees_show(): void
    {
        $contact = AdvertisingContact::factory()->create([
            'name'    => 'PepeTest',
            'message' => 'Quiero publicitar aquí',
        ]);

        $this->asAdmin()->get("/admin/advertising-contacts/{$contact->id}")
            ->assertOk()
            ->assertSee('PepeTest')
            ->assertSee('Quiero publicitar aquí');
    }

    public function test_admin_can_delete(): void
    {
        $contact = AdvertisingContact::factory()->create();
        $this->asAdmin()->delete("/admin/advertising-contacts/{$contact->id}")->assertRedirect();
        $this->assertDatabaseMissing('advertising_contacts', ['id' => $contact->id]);
    }

    public function test_moderator_is_forbidden(): void
    {
        $contact = AdvertisingContact::factory()->create();
        $this->asModerator()->get('/admin/advertising-contacts')->assertForbidden();
        $this->asModerator()->get("/admin/advertising-contacts/{$contact->id}")->assertForbidden();
        $this->asModerator()->delete("/admin/advertising-contacts/{$contact->id}")->assertForbidden();
    }
}
