<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisIntegrationTest extends TestCase
{
    public function test_redis_ping_responds_truthy(): void
    {
        // phpredis returns true, Predis returns 'PONG' — acepto ambos.
        $this->assertTrue((bool) Redis::ping());
    }

    public function test_cache_writes_and_reads_from_redis(): void
    {
        Cache::put('test:key', 'test-value', 60);
        $this->assertSame('test-value', Cache::get('test:key'));
        Cache::forget('test:key');
    }

    public function test_queue_default_connection_is_redis(): void
    {
        $this->assertSame('redis', config('queue.default'));
    }

    public function test_session_driver_is_redis(): void
    {
        $this->assertSame('redis', config('session.driver'));
    }
}
