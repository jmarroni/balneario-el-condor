<?php

namespace Tests\Feature\Etl;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyConnectionTest extends TestCase
{
    public function test_legacy_connection_is_registered(): void
    {
        $config = config('database.connections.legacy');

        $this->assertSame('mysql', $config['driver']);
        $this->assertSame('latin1', $config['charset']);
        $this->assertFalse($config['strict']);
    }

    public function test_base_etl_command_exists(): void
    {
        $this->assertTrue(class_exists(\App\Console\Commands\Etl\BaseEtlCommand::class));
    }

    public function test_etl_all_command_is_registered(): void
    {
        \Illuminate\Support\Facades\Artisan::call('list');
        $output = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringContainsString('etl:all', $output);
    }
}
