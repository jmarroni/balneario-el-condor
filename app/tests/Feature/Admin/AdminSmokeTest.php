<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

class AdminSmokeTest extends AdminTestCase
{
    public function test_admin_navigates_all_indexes_and_dashboard(): void
    {
        $this->asAdmin();

        $routes = [
            'admin.dashboard',
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

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "smoke falló en {$route}: got {$response->getStatusCode()}"
            );
        }
    }

    public function test_admin_can_access_create_forms(): void
    {
        $this->asAdmin();

        $creates = [
            'admin.news.create',
            'admin.events.create',
            'admin.pages.create',
            'admin.recipes.create',
            'admin.lodgings.create',
            'admin.venues.create',
            'admin.rentals.create',
            'admin.service-providers.create',
            'admin.nearby-places.create',
            'admin.useful-info.create',
            'admin.tides.create',
            'admin.classifieds.create',
            'admin.gallery.create',
            'admin.surveys.create',
            'admin.newsletter-campaigns.create',
            'admin.users.create',
        ];

        foreach ($creates as $route) {
            $response = $this->get(route($route));
            $this->assertSame(
                200,
                $response->getStatusCode(),
                "create form falló en {$route}: got {$response->getStatusCode()}"
            );
        }
    }
}
