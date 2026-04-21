<?php

namespace Tests\Feature\Models;

use App\Models\Media;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaPolymorphicTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_belongs_to_any_mediable_model(): void
    {
        $category = NewsCategory::factory()->create();
        $news = News::factory()->create(['news_category_id' => $category->id]);

        $media = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'uploads/news/test.jpg',
            'alt'           => 'Test image',
            'type'          => 'image',
            'sort_order'    => 0,
        ]);

        $this->assertTrue($media->mediable->is($news));
        $this->assertCount(1, $news->media);
    }

    public function test_media_sort_order_default_zero(): void
    {
        $category = NewsCategory::factory()->create();
        $news = News::factory()->create(['news_category_id' => $category->id]);

        $media = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'uploads/news/test.jpg',
        ]);

        $this->assertSame(0, $media->sort_order);
    }
}
