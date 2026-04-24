<?php

namespace Database\Seeders;

use App\Models\{
    AdvertisingContact, Classified, ClassifiedCategory, ClassifiedContact,
    ContactMessage, Event, EventRegistration, GalleryImage, Lodging,
    Media, NearbyPlace, News, NewsCategory, NewsletterCampaign,
    NewsletterSubscriber, Page, Recipe, Rental, ServiceProvider,
    Survey, SurveyResponse, Tide, UsefulInfo, Venue,
};
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Taxonomías
        $newsCats       = NewsCategory::factory()->count(4)->create();
        $classifiedCats = ClassifiedCategory::factory()->count(6)->create();

        // Contenido editorial
        News::factory()->count(10)->recycle($newsCats)->create();
        Event::factory()->count(8)->create()->each(function ($event) {
            if ($event->accepts_registrations) {
                EventRegistration::factory()->count(5)->create(['event_id' => $event->id]);
            }
        });

        // Directorios
        Lodging::factory()->count(10)->create();
        Venue::factory()->count(8)->create();
        Rental::factory()->count(6)->create();
        Classified::factory()->count(10)->recycle($classifiedCats)->create()->each(function ($classified) {
            ClassifiedContact::factory()->count(fake()->numberBetween(0, 3))->create([
                'classified_id' => $classified->id,
            ]);
        });
        ServiceProvider::factory()->count(10)->create();
        Recipe::factory()->count(10)->create();
        GalleryImage::factory()->count(20)->create();
        NearbyPlace::factory()->count(6)->create();
        UsefulInfo::factory()->count(8)->create();
        Page::factory()->count(6)->create();

        // Mareas: 30 días consecutivos desde hoy
        $today = now();
        for ($i = 0; $i < 30; $i++) {
            Tide::factory()->create(['date' => $today->copy()->addDays($i)->format('Y-m-d')]);
        }

        // Engagement
        $survey = Survey::factory()->create();
        SurveyResponse::factory()->count(40)->create(['survey_id' => $survey->id]);
        NewsletterSubscriber::factory()->count(50)->create();
        NewsletterCampaign::factory()->count(5)->create();
        ContactMessage::factory()->count(15)->create();
        AdvertisingContact::factory()->count(8)->create();

        // Media demo: agregar 1-3 imágenes a cada contenido visual
        foreach (News::all() as $n) {
            Media::factory()->count(fake()->numberBetween(1, 3))->create([
                'mediable_id'   => $n->id,
                'mediable_type' => News::class,
            ]);
        }
        foreach (Lodging::all() as $l) {
            Media::factory()->count(fake()->numberBetween(2, 5))->create([
                'mediable_id'   => $l->id,
                'mediable_type' => Lodging::class,
            ]);
        }
        foreach (Venue::all() as $v) {
            Media::factory()->count(fake()->numberBetween(1, 4))->create([
                'mediable_id'   => $v->id,
                'mediable_type' => Venue::class,
            ]);
        }
        foreach (Classified::all() as $c) {
            Media::factory()->count(fake()->numberBetween(0, 3))->create([
                'mediable_id'   => $c->id,
                'mediable_type' => Classified::class,
            ]);
        }
    }
}
