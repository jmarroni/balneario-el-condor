<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Survey;
use App\Models\SurveyResponse;

class AdminSurveyResponsesTest extends AdminTestCase
{
    public function test_admin_views_index(): void
    {
        $survey = Survey::factory()->create();
        SurveyResponse::factory()->count(3)->create(['survey_id' => $survey->id]);

        $this->asAdmin()->get("/admin/surveys/{$survey->id}/responses")
            ->assertOk()
            ->assertSee('Respuestas');
    }

    public function test_moderator_is_forbidden(): void
    {
        $survey = Survey::factory()->create();
        $this->asModerator()->get("/admin/surveys/{$survey->id}/responses")->assertForbidden();
    }

    public function test_admin_can_delete(): void
    {
        $response = SurveyResponse::factory()->create();
        $this->asAdmin()->delete("/admin/responses/{$response->id}")->assertRedirect();
        $this->assertDatabaseMissing('survey_responses', ['id' => $response->id]);
    }
}
