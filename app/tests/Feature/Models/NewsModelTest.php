<?php

namespace Tests\Feature\Models;

use App\Models\Media;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_belongs_to_category(): void
    {
        $category = NewsCategory::factory()->create();
        $news = News::factory()->create(['news_category_id' => $category->id]);

        $this->assertTrue($news->category->is($category));
    }

    public function test_news_has_many_media_ordered_by_sort_order(): void
    {
        $news = News::factory()->create();

        $second = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'p2.jpg',
            'sort_order'    => 2,
        ]);
        $first = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'p1.jpg',
            'sort_order'    => 1,
        ]);

        $ordered = $news->media->pluck('path')->all();
        $this->assertSame(['p1.jpg', 'p2.jpg'], $ordered);
    }

    public function test_published_at_is_datetime_cast(): void
    {
        $news = News::factory()->create(['published_at' => '2026-04-21 10:00:00']);
        $this->assertInstanceOf(\DateTimeInterface::class, $news->published_at);
    }

    public function test_soft_delete_preserves_row(): void
    {
        $news = News::factory()->create();
        $news->delete();

        $this->assertSoftDeleted('news', ['id' => $news->id]);
    }
}
