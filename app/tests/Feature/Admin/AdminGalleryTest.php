<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\GalleryImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AdminGalleryTest extends AdminTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_sees_index(): void
    {
        GalleryImage::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/gallery')->assertOk()->assertSee('Galería');
    }

    public function test_moderator_is_forbidden(): void
    {
        // gallery NO está en MODERABLE_MODULES
        $this->asModerator()->get('/admin/gallery')->assertForbidden();
    }

    public function test_editor_can_upload_image(): void
    {
        $file = UploadedFile::fake()->image('foto.jpg', 400, 300);

        $response = $this->asEditor()->post('/admin/gallery', [
            'title' => 'Atardecer',
            'description' => 'Una hermosa foto',
            'image' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('gallery_images', ['title' => 'Atardecer']);

        $image = GalleryImage::where('title', 'Atardecer')->firstOrFail();
        $this->assertNotEmpty($image->path);
        $this->assertNotEmpty($image->thumb_path);
        $this->assertNotEmpty($image->original_path);

        Storage::disk('public')->assertExists($image->path);
        Storage::disk('public')->assertExists($image->thumb_path);
    }

    public function test_store_requires_image(): void
    {
        $this->asEditor()->post('/admin/gallery', [
            'title' => 'Sin imagen',
        ])->assertSessionHasErrors(['image']);
    }

    public function test_admin_can_update_without_new_image(): void
    {
        $image = GalleryImage::factory()->create([
            'title' => 'Original',
            'path' => 'gallery/old.jpg',
        ]);

        $this->asAdmin()->put("/admin/gallery/{$image->id}", [
            'title' => 'Editado',
        ])->assertRedirect();

        $image->refresh();
        $this->assertSame('Editado', $image->title);
        $this->assertSame('gallery/old.jpg', $image->path);
    }

    public function test_admin_can_update_with_new_image(): void
    {
        $image = GalleryImage::factory()->create();
        $newFile = UploadedFile::fake()->image('nueva.jpg', 300, 200);

        $this->asAdmin()->put("/admin/gallery/{$image->id}", [
            'title' => $image->title,
            'image' => $newFile,
        ])->assertRedirect();

        $image->refresh();
        $this->assertNotEmpty($image->path);
        Storage::disk('public')->assertExists($image->path);
        Storage::disk('public')->assertExists($image->thumb_path);
    }

    public function test_admin_can_delete(): void
    {
        $image = GalleryImage::factory()->create();

        $this->asAdmin()->delete("/admin/gallery/{$image->id}")->assertRedirect();
        $this->assertDatabaseMissing('gallery_images', ['id' => $image->id]);
    }
}
