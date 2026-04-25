<?php

declare(strict_types=1);

namespace Tests\Feature\Hardening;

use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class DocsRestrictionTest extends TestCase
{
    public function test_docs_accessible_in_local_env(): void
    {
        Config::set('app.env', 'local');

        // Smoke: la ruta debe responder. Si index.html existe, OK; si no, 404 sería normal.
        $response = $this->get('/docs');

        $this->assertContains($response->getStatusCode(), [200, 404]);
    }

    public function test_docs_returns_404_in_production_when_disabled(): void
    {
        Config::set('app.env', 'production');
        Config::set('scribe.docs_enabled', false);

        $this->get('/docs')->assertNotFound();
    }

    public function test_docs_requires_basic_auth_in_production_when_enabled(): void
    {
        Config::set('app.env', 'production');
        Config::set('scribe.docs_enabled', true);
        Config::set('scribe.docs_basic_auth_user', 'admin');
        Config::set('scribe.docs_basic_auth_pass', 'secret');

        $this->get('/docs')->assertStatus(401);
    }

    public function test_docs_serves_when_basic_auth_correct(): void
    {
        Config::set('app.env', 'production');
        Config::set('scribe.docs_enabled', true);
        Config::set('scribe.docs_basic_auth_user', 'admin');
        Config::set('scribe.docs_basic_auth_pass', 'secret');

        $response = $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode('admin:secret'),
        ])->get('/docs');

        // 200 si existe el file (artifact ya generado), 404 si no.
        $this->assertContains($response->getStatusCode(), [200, 404]);
    }

    public function test_docs_rejects_wrong_password(): void
    {
        Config::set('app.env', 'production');
        Config::set('scribe.docs_enabled', true);
        Config::set('scribe.docs_basic_auth_user', 'admin');
        Config::set('scribe.docs_basic_auth_pass', 'secret');

        $response = $this->withHeaders([
            'Authorization' => 'Basic '.base64_encode('admin:wrong'),
        ])->get('/docs');

        $response->assertStatus(401);
    }

    public function test_docs_enabled_without_basic_auth_serves_freely(): void
    {
        Config::set('app.env', 'production');
        Config::set('scribe.docs_enabled', true);
        Config::set('scribe.docs_basic_auth_user', null);
        Config::set('scribe.docs_basic_auth_pass', null);

        $response = $this->get('/docs');

        $this->assertContains($response->getStatusCode(), [200, 404]);
    }

    public function test_up_healthcheck_returns_200(): void
    {
        $this->get('/up')->assertOk();
    }
}
