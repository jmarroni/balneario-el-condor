<?php

namespace Tests\Feature\Models;

use App\Models\Lodging;
use App\Models\Media;
use App\Models\Rental;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LodgingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_lodging_morph_media(): void
    {
        $lodging = Lodging::factory()->create();
        Media::factory()->create([
            'mediable_id'   => $lodging->id,
            'mediable_type' => Lodging::class,
            'path'          => 'l.jpg',
        ]);

        $this->assertCount(1, $lodging->fresh()->media);
    }

    public function test_venue_has_category_and_media(): void
    {
        $venue = Venue::factory()->create(['category' => 'gourmet']);
        $this->assertSame('gourmet', $venue->category);

        Media::factory()->create([
            'mediable_id'   => $venue->id,
            'mediable_type' => Venue::class,
            'path'          => 'v.jpg',
        ]);

        $this->assertCount(1, $venue->fresh()->media);
    }

    public function test_rental_has_places_and_media(): void
    {
        $rental = Rental::factory()->create(['places' => 4]);
        $this->assertSame(4, $rental->places);

        Media::factory()->count(3)->create([
            'mediable_id'   => $rental->id,
            'mediable_type' => Rental::class,
        ]);

        $this->assertCount(3, $rental->fresh()->media);
    }

    public function test_all_three_soft_delete(): void
    {
        $l = Lodging::factory()->create(); $l->delete(); $this->assertSoftDeleted('lodgings', ['id' => $l->id]);
        $v = Venue::factory()->create();   $v->delete(); $this->assertSoftDeleted('venues',   ['id' => $v->id]);
        $r = Rental::factory()->create();  $r->delete(); $this->assertSoftDeleted('rentals',  ['id' => $r->id]);
    }
}
