<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportTidesRequest;
use App\Models\Tide;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TideImportController extends Controller
{
    use AuthorizesRequests;

    private const EXPECTED_HEADERS = [
        'date',
        'first_high', 'first_high_height',
        'first_low', 'first_low_height',
        'second_high', 'second_high_height',
        'second_low', 'second_low_height',
    ];

    public function form(): View
    {
        $this->authorize('create', Tide::class);

        return view('admin.tides.import');
    }

    public function import(ImportTidesRequest $request): RedirectResponse
    {
        $this->authorize('create', Tide::class);

        $location = $request->input('location') ?: 'El Cóndor';
        $path     = $request->file('file')->getRealPath();

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->withErrors(['file' => 'No se pudo abrir el archivo.']);
        }

        $headers = fgetcsv($handle);
        if (! $headers) {
            fclose($handle);

            return back()->withErrors(['file' => 'El archivo está vacío.']);
        }

        $headers = array_map(static fn ($h) => strtolower(trim((string) $h)), $headers);
        $missing = array_diff(self::EXPECTED_HEADERS, $headers);
        if (! empty($missing)) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'Faltan columnas: ' . implode(', ', $missing),
            ]);
        }

        $created = 0;
        $updated = 0;
        $errors  = [];
        $line    = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if (count($row) === 1 && $row[0] === null) {
                continue; // línea vacía
            }

            $data = [];
            foreach ($headers as $i => $h) {
                $data[$h] = isset($row[$i]) ? trim((string) $row[$i]) : null;
                if ($data[$h] === '') {
                    $data[$h] = null;
                }
            }

            if (empty($data['date'])) {
                $errors[] = "Línea {$line}: fecha vacía.";
                continue;
            }

            try {
                $attributes = [
                    'first_high'         => $data['first_high'] ?? null,
                    'first_high_height'  => $data['first_high_height'] ?? null,
                    'first_low'          => $data['first_low'] ?? null,
                    'first_low_height'   => $data['first_low_height'] ?? null,
                    'second_high'        => $data['second_high'] ?? null,
                    'second_high_height' => $data['second_high_height'] ?? null,
                    'second_low'         => $data['second_low'] ?? null,
                    'second_low_height'  => $data['second_low_height'] ?? null,
                ];

                $existing = Tide::where('location', $location)
                    ->whereDate('date', $data['date'])
                    ->first();

                if ($existing) {
                    $existing->update($attributes);
                    $updated++;
                } else {
                    Tide::create(array_merge([
                        'location' => $location,
                        'date'     => $data['date'],
                    ], $attributes));
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors[] = "Línea {$line}: {$e->getMessage()}";
            }
        }

        fclose($handle);

        $message = "Importación completa. Creadas: {$created}, actualizadas: {$updated}.";
        if (! empty($errors)) {
            $message .= ' Errores: ' . count($errors);
        }

        return redirect()
            ->route('admin.tides.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }
}
