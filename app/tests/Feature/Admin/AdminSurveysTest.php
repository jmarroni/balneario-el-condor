<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Survey;

class AdminSurveysTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Survey::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/surveys')->assertOk()->assertSee('Encuestas');
    }

    public function test_moderator_is_forbidden(): void
    {
        $this->asModerator()->get('/admin/surveys')->assertForbidden();
    }

    public function test_admin_can_create(): void
    {
        $response = $this->asAdmin()->post('/admin/surveys', [
            'title'    => 'Encuesta de prueba',
            'question' => '¿Cómo nos conociste?',
            'options'  => [
                ['key' => 1, 'label' => 'Google'],
                ['key' => 2, 'label' => 'Amigos'],
            ],
            'active'   => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('surveys', ['title' => 'Encuesta de prueba']);
    }

    public function test_store_validates_required(): void
    {
        $this->asAdmin()->post('/admin/surveys', [])
            ->assertSessionHasErrors(['title', 'question', 'options']);
    }

    public function test_store_validates_min_two_options(): void
    {
        $this->asAdmin()->post('/admin/surveys', [
            'title'    => 'Una opción',
            'question' => '¿Única?',
            'options'  => [['key' => 1, 'label' => 'Solo']],
        ])->assertSessionHasErrors(['options']);
    }

    public function test_admin_can_update(): void
    {
        $survey = Survey::factory()->create();
        $this->asAdmin()->put("/admin/surveys/{$survey->id}", [
            'title'    => 'Nuevo título',
            'question' => $survey->question,
            'options'  => [
                ['key' => 1, 'label' => 'A'],
                ['key' => 2, 'label' => 'B'],
            ],
        ])->assertRedirect();
        $this->assertDatabaseHas('surveys', ['id' => $survey->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $survey = Survey::factory()->create();
        $this->asAdmin()->delete("/admin/surveys/{$survey->id}")->assertRedirect();
        $this->assertSoftDeleted('surveys', ['id' => $survey->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $survey = Survey::factory()->create();
        $this->asModerator()->delete("/admin/surveys/{$survey->id}")->assertForbidden();
    }
}
