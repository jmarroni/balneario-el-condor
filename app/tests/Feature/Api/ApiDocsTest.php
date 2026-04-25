<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    public function test_api_docs_accessible_when_generated(): void
    {
        $docsPath = public_path('docs/index.html');

        if (! is_file($docsPath)) {
            $this->markTestSkipped('Scribe docs not generated. Run: php artisan scribe:generate');
        }

        $html = (string) file_get_contents($docsPath);

        $this->assertStringContainsString('Balneario El Condor', $html);
        $this->assertStringContainsString('API Documentation', $html);
    }

    public function test_openapi_spec_is_generated(): void
    {
        $specPath = public_path('docs/openapi.yaml');

        if (! is_file($specPath)) {
            $this->markTestSkipped('Scribe OpenAPI spec not generated.');
        }

        $yaml = (string) file_get_contents($specPath);

        $this->assertStringContainsString('openapi: 3.0', $yaml);
        $this->assertStringContainsString('Balneario El Condor', $yaml);
        $this->assertStringContainsString('/api/v1/me', $yaml);
        $this->assertStringContainsString('/api/v1/contact', $yaml);
    }
}
