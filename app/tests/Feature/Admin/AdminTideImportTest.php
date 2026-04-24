<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Tide;
use Illuminate\Http\UploadedFile;

class AdminTideImportTest extends AdminTestCase
{
    public function test_admin_can_upload_valid_csv(): void
    {
        $csv = implode("\n", [
            'date,first_high,first_high_height,first_low,first_low_height,second_high,second_high_height,second_low,second_low_height',
            '2026-06-01,08:15,3.20 m,14:30,0.80 m,20:45,3.10 m,02:30,0.90 m',
            '2026-06-02,09:00,3.30 m,15:10,0.70 m,21:20,3.20 m,03:10,0.85 m',
        ]);

        $file = UploadedFile::fake()->createWithContent('mareas.csv', $csv);

        $response = $this->asAdmin()->post('/admin/tides/import', [
            'file'     => $file,
            'location' => 'El Cóndor',
        ]);

        $response->assertRedirect(route('admin.tides.index'));

        $this->assertDatabaseHas('tides', [
            'date'     => '2026-06-01',
            'location' => 'El Cóndor',
        ]);
        $this->assertDatabaseHas('tides', [
            'date'     => '2026-06-02',
            'location' => 'El Cóndor',
        ]);
        $this->assertSame(2, Tide::count());
    }

    public function test_invalid_csv_fails_validation(): void
    {
        $file = UploadedFile::fake()->create('no-es-csv.pdf', 10, 'application/pdf');

        $this->asAdmin()->post('/admin/tides/import', [
            'file' => $file,
        ])->assertSessionHasErrors(['file']);
    }

    public function test_moderator_cannot_import(): void
    {
        $csv  = "date,first_high,first_high_height,first_low,first_low_height,second_high,second_high_height,second_low,second_low_height\n2026-06-01,08:15,3.20 m,14:30,0.80 m,20:45,3.10 m,02:30,0.90 m";
        $file = UploadedFile::fake()->createWithContent('mareas.csv', $csv);

        $this->asModerator()->post('/admin/tides/import', [
            'file' => $file,
        ])->assertForbidden();
    }
}
