<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

class AdminRolesMatrixTest extends AdminTestCase
{
    /**
     * @return list<string>
     */
    private function allIndexes(): array
    {
        return [
            'admin.news.index',
            'admin.events.index',
            'admin.pages.index',
            'admin.recipes.index',
            'admin.lodgings.index',
            'admin.venues.index',
            'admin.rentals.index',
            'admin.service-providers.index',
            'admin.nearby-places.index',
            'admin.useful-info.index',
            'admin.tides.index',
            'admin.classifieds.index',
            'admin.gallery.index',
            'admin.surveys.index',
            'admin.newsletter-subscribers.index',
            'admin.newsletter-campaigns.index',
            'admin.contact-messages.index',
            'admin.advertising-contacts.index',
            'admin.users.index',
        ];
    }

    public function test_admin_can_access_all_module_indexes(): void
    {
        $this->asAdmin();
        foreach ($this->allIndexes() as $route) {
            $response = $this->get(route($route));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "admin debería poder acceder a {$route}, got {$response->getStatusCode()}"
            );
        }
    }

    public function test_editor_forbidden_on_users(): void
    {
        $this->asEditor()
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_editor_can_access_non_user_indexes(): void
    {
        $this->asEditor();
        foreach ($this->allIndexes() as $route) {
            if ($route === 'admin.users.index') {
                continue;
            }
            $response = $this->get(route($route));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "editor debería poder acceder a {$route}, got {$response->getStatusCode()}"
            );
        }
    }

    public function test_moderator_forbidden_on_non_moderable(): void
    {
        $forbidden = [
            'admin.news.index',
            'admin.events.index',
            'admin.pages.index',
            'admin.recipes.index',
            'admin.lodgings.index',
            'admin.venues.index',
            'admin.rentals.index',
            'admin.service-providers.index',
            'admin.nearby-places.index',
            'admin.useful-info.index',
            'admin.tides.index',
            'admin.gallery.index',
            'admin.surveys.index',
            'admin.newsletter-campaigns.index',
            'admin.advertising-contacts.index',
            'admin.users.index',
        ];

        $this->asModerator();
        foreach ($forbidden as $route) {
            $response = $this->get(route($route));
            $this->assertSame(
                403,
                $response->getStatusCode(),
                "moderator no debería poder acceder a {$route}, got {$response->getStatusCode()}"
            );
        }
    }

    public function test_moderator_allowed_on_moderable(): void
    {
        $allowed = [
            'admin.classifieds.index',
            'admin.contact-messages.index',
            'admin.newsletter-subscribers.index',
        ];

        $this->asModerator();
        foreach ($allowed as $route) {
            $response = $this->get(route($route));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "moderator debería poder acceder a {$route}, got {$response->getStatusCode()}"
            );
        }
    }
}
