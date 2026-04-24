<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Classified;
use App\Models\ClassifiedCategory;
use App\Models\ClassifiedContact;
use App\Models\GalleryImage;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_classifieds_index_shows_items(): void
    {
        Classified::factory()->create([
            'title' => 'Vendo lancha 5 metros con motor',
            'slug'  => 'vendo-lancha-5-metros-con-motor',
            'description' => 'Lancha de aluminio en muy buen estado, motor 25 HP.',
        ]);

        $this->get('/clasificados')
            ->assertOk()
            ->assertSee('Clasificados')
            ->assertSee('Vendo lancha 5 metros con motor');
    }

    public function test_classifieds_filter_by_category(): void
    {
        $vehiculos = ClassifiedCategory::factory()->create([
            'name' => 'Vehículos',
            'slug' => 'vehiculos',
        ]);
        $inmuebles = ClassifiedCategory::factory()->create([
            'name' => 'Inmuebles',
            'slug' => 'inmuebles',
        ]);

        Classified::factory()->create([
            'title' => 'Vendo Renault Kangoo Test',
            'slug'  => 'vendo-renault-kangoo-test',
            'classified_category_id' => $vehiculos->id,
        ]);
        Classified::factory()->create([
            'title' => 'Alquilo cabaña frente al mar Test',
            'slug'  => 'alquilo-cabana-frente-al-mar-test',
            'classified_category_id' => $inmuebles->id,
        ]);

        $r1 = $this->get('/clasificados?categoria=vehiculos')->assertOk();
        $r1->assertSee('Vendo Renault Kangoo Test');
        $r1->assertDontSee('Alquilo cabaña frente al mar Test');

        $r2 = $this->get('/clasificados?categoria=inmuebles')->assertOk();
        $r2->assertSee('Alquilo cabaña frente al mar Test');
        $r2->assertDontSee('Vendo Renault Kangoo Test');
    }

    public function test_classifieds_show_renders_with_contact_form(): void
    {
        $classified = Classified::factory()->create([
            'title'         => 'Ofrezco clases de natación verano',
            'slug'          => 'ofrezco-clases-de-natacion-verano',
            'description'   => 'Clases para todas las edades en la pileta municipal.',
            'contact_name'  => 'Marina Costa',
            'contact_email' => 'marina@example.test',
        ]);

        $this->get(route('clasificados.show', $classified))
            ->assertOk()
            ->assertSee('Ofrezco clases de natación verano')
            ->assertSee('Clases para todas las edades')
            ->assertSee('Marina Costa')
            ->assertSee('Contactar al anunciante')
            ->assertSee('name="message"', false)
            ->assertSee('name="email"', false);
    }

    public function test_classifieds_contact_form_validates(): void
    {
        $classified = Classified::factory()->create([
            'slug' => 'aviso-test-validacion',
            'contact_email' => 'owner@example.test',
        ]);

        $response = $this->from(route('clasificados.show', $classified))
            ->post(route('clasificados.contact', $classified), [
                'name'    => '',
                'email'   => 'no-es-email',
                'message' => 'corto',
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('classified_contacts', 0);
    }

    public function test_classifieds_contact_form_creates_record(): void
    {
        $classified = Classified::factory()->create([
            'slug' => 'aviso-test-contacto-ok',
            'contact_email' => 'duenio@example.test',
        ]);

        $response = $this->post(route('clasificados.contact', $classified), [
            'name'    => 'Pedro Vecino',
            'email'   => 'pedro@example.test',
            'phone'   => '+54 9 2920 111222',
            'message' => 'Hola, me interesa lo que ofrecés. ¿Sigue disponible?',
        ]);

        $response->assertRedirect(route('clasificados.show', $classified));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('classified_contacts', [
            'classified_id'     => $classified->id,
            'contact_name'      => 'Pedro Vecino',
            'contact_email'     => 'pedro@example.test',
            'contact_phone'     => '+54 9 2920 111222',
            'destination_email' => 'duenio@example.test',
        ]);

        $contact = ClassifiedContact::where('classified_id', $classified->id)->firstOrFail();
        $this->assertNull($contact->legacy_id, 'Form submissions deben tener legacy_id null.');
    }

    public function test_gallery_index_shows_images(): void
    {
        GalleryImage::factory()->create([
            'title'    => 'Atardecer único en el faro',
            'slug'     => 'atardecer-unico-en-el-faro',
            'path'     => 'gallery/test-atardecer-faro.jpg',
            'taken_on' => '2024-03-10',
        ]);

        $this->get('/galeria')
            ->assertOk()
            ->assertSee('Galería')
            ->assertSee('Atardecer único en el faro');
    }

    public function test_recipes_index_shows_items(): void
    {
        Recipe::factory()->create([
            'title' => 'Mejillones a la provenzal del puerto',
            'slug'  => 'mejillones-a-la-provenzal-del-puerto',
            'author' => 'Doña Elsa',
        ]);

        $this->get('/recetas')
            ->assertOk()
            ->assertSee('Recetario')
            ->assertSee('Mejillones a la provenzal del puerto')
            ->assertSee('Doña Elsa');
    }

    public function test_recipes_show_renders_ingredients_and_instructions(): void
    {
        $recipe = Recipe::factory()->create([
            'title'        => 'Sopa de pescado patagónica con mariscos',
            'slug'         => 'sopa-de-pescado-patagonica-con-mariscos',
            'prep_minutes' => 20,
            'cook_minutes' => 40,
            'servings'     => '6 porciones',
            'cost'         => 'Medio',
            'author'       => 'Cocina del Sur',
            'ingredients'  => "1 kg de pescado blanco\n500 g de mejillones\n2 zanahorias\n1 cebolla grande",
            'instructions' => "Limpiar bien el pescado y trozarlo.\n\nSaltear la cebolla con las zanahorias hasta dorar.\n\nAgregar el pescado y los mejillones, cocinar 20 minutos a fuego bajo.",
        ]);

        $response = $this->get(route('recetas.show', $recipe))->assertOk();

        $response->assertSee('Sopa de pescado patagónica con mariscos');
        $response->assertSee('Cocina del Sur');
        $response->assertSee('Ingredientes');
        $response->assertSee('1 kg de pescado blanco');
        $response->assertSee('500 g de mejillones');
        $response->assertSee('Preparación');
        $response->assertSee('Limpiar bien el pescado');
        $response->assertSee('Saltear la cebolla');
        // Numeración 01 / 02 / 03.
        $response->assertSee('01');
        $response->assertSee('02');
        $response->assertSee('03');
        // Meta tiempos.
        $response->assertSee('20 min');
        $response->assertSee('40 min');
        $response->assertSee('6 porciones');
    }
}
