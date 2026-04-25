<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationRollbackTest extends TestCase
{
    protected function tearDown(): void
    {
        Artisan::call('migrate:fresh', ['--force' => true]);
        RefreshDatabaseState::$migrated = false;

        parent::tearDown();
    }

    public function test_all_custom_tables_exist_after_migrate(): void
    {
        Artisan::call('migrate:fresh', ['--force' => true]);

        $expected = [
            'media',
            'news', 'news_categories',
            'events', 'event_registrations',
            'lodgings', 'venues', 'rentals',
            'classifieds', 'classified_categories', 'classified_contacts',
            'service_providers', 'recipes', 'gallery_images',
            'nearby_places', 'useful_info', 'pages', 'tides',
            'surveys', 'survey_responses',
            'newsletter_subscribers', 'newsletter_campaigns',
            'contact_messages', 'advertising_contacts',
        ];

        foreach ($expected as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} should exist after migrate");
        }
    }

    public function test_rollback_drops_all_custom_tables_cleanly(): void
    {
        Artisan::call('migrate:fresh', ['--force' => true]);
        $exit = Artisan::call('migrate:rollback', ['--step' => 36, '--force' => true]);

        $this->assertSame(0, $exit, 'migrate:rollback should exit 0');

        $droppedCheck = ['news', 'events', 'lodgings', 'classifieds', 'media'];
        foreach ($droppedCheck as $table) {
            $this->assertFalse(
                Schema::hasTable($table),
                "Table {$table} should be dropped after rollback"
            );
        }
    }
}
