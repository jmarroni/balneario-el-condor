<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Classified;
use App\Models\ClassifiedContact;

class AdminClassifiedContactsTest extends AdminTestCase
{
    public function test_admin_sees_index_for_classified(): void
    {
        $classified = Classified::factory()->create();
        ClassifiedContact::factory()->count(3)->create(['classified_id' => $classified->id]);
        ClassifiedContact::factory()->count(2)->create(); // otro classified

        $response = $this->asAdmin()->get("/admin/classifieds/{$classified->id}/contacts");
        $response->assertOk()->assertSee('Contactos');
    }

    public function test_admin_sees_show(): void
    {
        $contact = ClassifiedContact::factory()->create([
            'contact_name' => 'Pedro Test',
            'message' => 'Mensaje de prueba',
        ]);

        $this->asAdmin()->get("/admin/contacts/{$contact->id}")
            ->assertOk()
            ->assertSee('Pedro Test')
            ->assertSee('Mensaje de prueba');
    }

    public function test_admin_can_delete(): void
    {
        $contact = ClassifiedContact::factory()->create();

        $this->asAdmin()->delete("/admin/contacts/{$contact->id}")->assertRedirect();
        $this->assertDatabaseMissing('classified_contacts', ['id' => $contact->id]);
    }

    public function test_moderator_is_forbidden(): void
    {
        $classified = Classified::factory()->create();
        $contact = ClassifiedContact::factory()->create(['classified_id' => $classified->id]);

        // classified_contacts NO está en MODERABLE_MODULES
        $this->asModerator()->get("/admin/classifieds/{$classified->id}/contacts")->assertForbidden();
        $this->asModerator()->get("/admin/contacts/{$contact->id}")->assertForbidden();
        $this->asModerator()->delete("/admin/contacts/{$contact->id}")->assertForbidden();
    }

    public function test_editor_can_see_and_delete(): void
    {
        $classified = Classified::factory()->create();
        $contact = ClassifiedContact::factory()->create(['classified_id' => $classified->id]);

        $this->asEditor()->get("/admin/classifieds/{$classified->id}/contacts")->assertOk();
        $this->asEditor()->delete("/admin/contacts/{$contact->id}")->assertRedirect();
        $this->assertDatabaseMissing('classified_contacts', ['id' => $contact->id]);
    }
}
