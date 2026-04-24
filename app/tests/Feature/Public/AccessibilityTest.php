<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Accesibilidad básica del sitio público (Fase 5 / Task 10).
 *
 * No reemplaza una auditoría axe completa, pero verifica los baselines críticos:
 * skip-link, landmark <main>, alt en imágenes, labels en inputs.
 */
class AccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_images_in_home_have_alt_or_role_presentation(): void
    {
        $response = $this->get('/');
        $html     = $response->getContent();

        preg_match_all('/<img[^>]*>/i', $html, $matches);
        foreach ($matches[0] as $img) {
            $this->assertMatchesRegularExpression(
                '/alt="[^"]*"|role="presentation"/',
                $img,
                "Imagen sin alt ni role=presentation: {$img}"
            );
        }
    }

    public function test_forms_have_labels_or_aria_label(): void
    {
        foreach (['/contacto', '/newsletter', '/publicite'] as $url) {
            $response = $this->get($url);
            $html     = $response->getContent();

            preg_match_all(
                '/<input[^>]*type="(?!hidden|submit|button)[^"]*"[^>]*>/',
                $html,
                $inputs
            );

            foreach ($inputs[0] as $input) {
                // Inputs honeypot/decorativos (aria-hidden="true") están intencionalmente
                // ocultos a AT y no requieren label.
                if (preg_match('/aria-hidden="true"/', $input)) {
                    continue;
                }

                $this->assertMatchesRegularExpression(
                    '/(id="[^"]+"|aria-label|aria-labelledby)/',
                    $input,
                    "Input sin label/aria: {$input} en {$url}"
                );
            }
        }
    }

    public function test_skip_link_exists_in_layout(): void
    {
        $response = $this->get('/');

        $response->assertSee('Saltar al contenido', false)
            ->assertSee('href="#main"', false);
    }

    public function test_main_landmark_exists(): void
    {
        $response = $this->get('/');

        $this->assertMatchesRegularExpression(
            '/<main[^>]*>/',
            $response->getContent(),
            'No se encontró landmark <main> en el layout público.'
        );
    }
}
