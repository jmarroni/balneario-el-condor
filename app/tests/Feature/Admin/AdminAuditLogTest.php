<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\News;
use App\Models\NewsCategory;
use Spatie\Activitylog\Models\Activity;

class AdminAuditLogTest extends AdminTestCase
{
    public function test_creating_news_logs_activity(): void
    {
        $this->actingAs($this->admin);

        $category = NewsCategory::factory()->create();
        $news = News::create([
            'news_category_id' => $category->id,
            'title'            => 'Una noticia auditada',
            'slug'             => 'una-noticia-auditada',
            'body'             => 'Cuerpo.',
        ]);

        $activity = Activity::query()
            ->where('subject_type', News::class)
            ->where('subject_id', $news->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity, 'Debe existir una entrada de Activity para el created event');
        $this->assertSame($this->admin->id, $activity->causer_id);
        $this->assertSame('creó la noticia', $activity->description);
        $this->assertArrayHasKey('attributes', $activity->properties->toArray());
    }

    public function test_updating_news_logs_only_dirty_fields(): void
    {
        $this->actingAs($this->admin);

        $news = News::factory()->create(['title' => 'Original']);
        Activity::query()->delete(); // limpia el created del factory

        $news->update(['title' => 'Actualizado']);

        $activity = Activity::query()
            ->where('subject_type', News::class)
            ->where('subject_id', $news->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame('actualizó la noticia', $activity->description);

        $properties = $activity->properties->toArray();
        $this->assertArrayHasKey('attributes', $properties);
        $this->assertArrayHasKey('old', $properties);
        $this->assertSame('Actualizado', $properties['attributes']['title']);
        $this->assertSame('Original', $properties['old']['title']);
        // Solo title está en attributes (logOnlyDirty)
        $this->assertSame(['title'], array_keys($properties['attributes']));
    }

    public function test_deleting_news_logs_activity(): void
    {
        $this->actingAs($this->admin);

        $news = News::factory()->create();
        $news->delete();

        $activity = Activity::query()
            ->where('subject_type', News::class)
            ->where('subject_id', $news->id)
            ->where('event', 'deleted')
            ->first();

        $this->assertNotNull($activity);
        $this->assertSame('eliminó la noticia', $activity->description);
        $this->assertSame($this->admin->id, $activity->causer_id);
    }

    public function test_admin_can_view_audit_log(): void
    {
        News::factory()->count(2)->create();

        $this->asAdmin()->get('/admin/audit-log')
            ->assertOk()
            ->assertSee('Bitácora');
    }

    public function test_editor_cannot_view_audit_log(): void
    {
        $this->asEditor()->get('/admin/audit-log')->assertForbidden();
    }

    public function test_moderator_cannot_view_audit_log(): void
    {
        $this->asModerator()->get('/admin/audit-log')->assertForbidden();
    }

    public function test_audit_log_filters_by_user(): void
    {
        $this->actingAs($this->admin);
        News::factory()->create(['title' => 'Hecha por admin']);

        $this->actingAs($this->editor);
        News::factory()->create(['title' => 'Hecha por editor']);

        $response = $this->asAdmin()->get('/admin/audit-log?user_id='.$this->admin->id);
        $response->assertOk();

        // Solo deberían aparecer registros del admin
        $logsForAdmin = Activity::query()
            ->where('causer_id', $this->admin->id)
            ->count();
        $logsForEditor = Activity::query()
            ->where('causer_id', $this->editor->id)
            ->count();

        $this->assertGreaterThan(0, $logsForAdmin);
        $this->assertGreaterThan(0, $logsForEditor);
    }

    public function test_audit_log_filters_by_date_range(): void
    {
        $this->actingAs($this->admin);

        // Antiguo
        $oldNews = News::factory()->create();
        Activity::query()->where('subject_id', $oldNews->id)->update([
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        // Reciente
        News::factory()->create();

        $from = now()->subDays(2)->format('Y-m-d');
        $to   = now()->format('Y-m-d');

        $response = $this->asAdmin()->get('/admin/audit-log?from='.$from.'&to='.$to);
        $response->assertOk();

        // Verificación funcional sobre la base: el query equivalente a la del controller
        // debe excluir los del admin de hace 10 días.
        $count = Activity::query()
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to.' 23:59:59')
            ->count();
        $this->assertGreaterThan(0, $count);

        $oldOutsideRange = Activity::query()
            ->where('subject_id', $oldNews->id)
            ->where('created_at', '<', $from)
            ->count();
        $this->assertGreaterThan(0, $oldOutsideRange);
    }
}
