<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Media;
use App\Models\News;
use App\Models\Survey;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminMediaTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_editor_can_upload_image(): void
    {
        $news = News::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $response = $this->asEditor()->post('/admin/media', [
            'file'          => $file,
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ]);

        $response->assertStatus(201)->assertJsonStructure(['id', 'url', 'sort_order']);

        $media = Media::query()->where('mediable_id', $news->id)->first();
        $this->assertNotNull($media);
        $this->assertSame(News::class, $media->mediable_type);
        $this->assertSame(0, $media->sort_order);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_upload_resizes_large_image_under_max_width(): void
    {
        $news = News::factory()->create();
        $file = UploadedFile::fake()->image('big.jpg', 2000, 1500);

        $this->asAdmin()->post('/admin/media', [
            'file'          => $file,
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertStatus(201);

        $media = Media::query()->latest('id')->first();
        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->path);

        $absolute = Storage::disk('public')->path($media->path);
        $info = getimagesize($absolute);
        $this->assertNotFalse($info);
        $this->assertLessThanOrEqual(1200, $info[0]);
    }

    public function test_upload_rejects_non_image_file(): void
    {
        $news = News::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $this->asEditor()->post('/admin/media', [
            'file'          => $file,
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertSessionHasErrors('file');
    }

    public function test_upload_rejects_unknown_mediable_type(): void
    {
        $news = News::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');

        $this->asEditor()->post('/admin/media', [
            'file'          => $file,
            'mediable_type' => Survey::class,
            'mediable_id'   => $news->id,
        ])->assertForbidden();
    }

    public function test_upload_rejects_user_without_update_permission(): void
    {
        $news = News::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');

        $this->asModerator()->post('/admin/media', [
            'file'          => $file,
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertForbidden();
    }

    public function test_sort_order_is_sequential_per_mediable(): void
    {
        $news = News::factory()->create();

        foreach (range(1, 3) as $i) {
            $this->asAdmin()->post('/admin/media', [
                'file'          => UploadedFile::fake()->image("p{$i}.jpg"),
                'mediable_type' => News::class,
                'mediable_id'   => $news->id,
            ])->assertStatus(201);
        }

        $orders = Media::query()
            ->where('mediable_id', $news->id)
            ->orderBy('id')
            ->pluck('sort_order')
            ->all();
        $this->assertSame([0, 1, 2], $orders);
    }

    public function test_editor_can_delete_media(): void
    {
        $news = News::factory()->create();
        $this->asAdmin()->post('/admin/media', [
            'file'          => UploadedFile::fake()->image('x.jpg'),
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertStatus(201);
        $media = Media::query()->first();

        $this->asEditor()->delete("/admin/media/{$media->id}")
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    public function test_moderator_cannot_delete_media_for_news(): void
    {
        $news = News::factory()->create();
        $this->asAdmin()->post('/admin/media', [
            'file'          => UploadedFile::fake()->image('x.jpg'),
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertStatus(201);
        $media = Media::query()->first();

        $this->asModerator()->delete("/admin/media/{$media->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_reorder_updates_sort_order(): void
    {
        $news = News::factory()->create();
        $ids = [];
        foreach (range(1, 3) as $i) {
            $res = $this->asAdmin()->post('/admin/media', [
                'file'          => UploadedFile::fake()->image("p{$i}.jpg"),
                'mediable_type' => News::class,
                'mediable_id'   => $news->id,
            ])->assertStatus(201);
            $ids[] = $res->json('id');
        }

        // Invertir el orden
        $payload = [
            ['id' => $ids[2], 'sort_order' => 0],
            ['id' => $ids[1], 'sort_order' => 1],
            ['id' => $ids[0], 'sort_order' => 2],
        ];

        $this->asAdmin()->patch('/admin/media/reorder', ['items' => $payload])
            ->assertOk();

        $this->assertSame(0, Media::find($ids[2])->sort_order);
        $this->assertSame(1, Media::find($ids[1])->sort_order);
        $this->assertSame(2, Media::find($ids[0])->sort_order);
    }

    public function test_reorder_requires_update_permission_on_mediable(): void
    {
        $news = News::factory()->create();
        $this->asAdmin()->post('/admin/media', [
            'file'          => UploadedFile::fake()->image('x.jpg'),
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertStatus(201);
        $media = Media::query()->first();

        $this->asModerator()->patch('/admin/media/reorder', [
            'items' => [['id' => $media->id, 'sort_order' => 0]],
        ])->assertForbidden();
    }

    public function test_admin_can_upload_delete_and_reorder(): void
    {
        $news = News::factory()->create();

        $this->asAdmin()->post('/admin/media', [
            'file'          => UploadedFile::fake()->image('a.jpg'),
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertStatus(201);

        $media = Media::query()->first();

        $this->asAdmin()->patch('/admin/media/reorder', [
            'items' => [['id' => $media->id, 'sort_order' => 5]],
        ])->assertOk();

        $this->assertSame(5, $media->fresh()->sort_order);

        $this->asAdmin()->delete("/admin/media/{$media->id}")->assertOk();
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_guest_cannot_upload(): void
    {
        $news = News::factory()->create();
        $this->post('/admin/media', [
            'file'          => UploadedFile::fake()->image('x.jpg'),
            'mediable_type' => News::class,
            'mediable_id'   => $news->id,
        ])->assertRedirect('/login');
    }
}
