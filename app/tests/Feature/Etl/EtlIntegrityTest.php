<?php

namespace Tests\Feature\Etl;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Tests\TestCase;

/**
 * @group etl-integrity
 * Solo corre si hay data migrada. Se saltea si la DB está vacía.
 */
class EtlIntegrityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (User::whereNotNull('legacy_id')->count() === 0) {
            $this->markTestSkipped('ETL no fue ejecutado — saltando');
        }
    }

    public function test_news_have_categories(): void
    {
        $orphan = News::whereDoesntHave('category')->count();
        $this->assertLessThan(News::count(), $orphan, 'casi todas las news deben tener category');
    }

    public function test_newsletter_emails_are_valid(): void
    {
        $invalid = NewsletterSubscriber::where('email', 'like', '%@legacy.invalid')
            ->orWhere('email', 'not like', '%@%')
            ->count();
        $this->assertSame(0, $invalid, 'no deberían quedar mails inválidos tras ETL');
    }
}
