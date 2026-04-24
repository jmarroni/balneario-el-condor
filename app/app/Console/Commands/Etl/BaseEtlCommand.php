<?php

namespace App\Console\Commands\Etl;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseEtlCommand extends Command
{
    /** Nombre de la tabla legacy (p.ej. 'novedades'). */
    protected string $legacyTable = '';

    /** Modelo Eloquent target (p.ej. \App\Models\News::class). */
    protected string $targetModel = '';

    /** Tamaño de chunk al leer del legacy. */
    protected int $chunkSize = 500;

    protected int $processed = 0;
    protected int $skipped   = 0;
    protected int $errors    = 0;

    /** Mapea una fila legacy a un array de atributos del modelo nuevo. */
    abstract protected function mapRow(object $row): ?array;

    /** Callback opcional post-create para relaciones (media, contacts, etc.). */
    protected function afterUpsert($model, object $row): void
    {
        // override en subclases
    }

    public function handle(): int
    {
        $this->info("[{$this->name}] Iniciando ETL de {$this->legacyTable} → " . class_basename($this->targetModel));

        $query = DB::connection('legacy')->table($this->legacyTable);
        $total = (clone $query)->count();
        $bar   = $this->output->createProgressBar($total);

        $query->orderBy($this->legacyPrimaryKey())->chunk($this->chunkSize, function ($rows) use ($bar) {
            foreach ($rows as $row) {
                $this->processRow($row);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->line("  procesadas: {$this->processed} | saltadas: {$this->skipped} | errores: {$this->errors}");

        return self::SUCCESS;
    }

    protected function processRow(object $row): void
    {
        try {
            $attrs = $this->mapRow($row);
            if ($attrs === null) {
                $this->skipped++;
                return;
            }

            $legacyId = $attrs['legacy_id'] ?? null;
            if ($legacyId === null) {
                Log::channel('etl')->warning('row sin legacy_id', ['table' => $this->legacyTable, 'row' => (array) $row]);
                $this->skipped++;
                return;
            }

            $model = ($this->targetModel)::updateOrCreate(
                ['legacy_id' => $legacyId],
                $attrs,
            );

            $this->afterUpsert($model, $row);
            $this->processed++;
        } catch (\Throwable $e) {
            $this->errors++;
            Log::channel('etl')->error('etl row error', [
                'table'   => $this->legacyTable,
                'row'     => (array) $row,
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
    }

    protected function legacyPrimaryKey(): string
    {
        // Cada subclase puede override; default busca primera columna
        return DB::connection('legacy')
            ->selectOne("SHOW KEYS FROM `{$this->legacyTable}` WHERE Key_name = 'PRIMARY'")
            ->Column_name ?? 'id';
    }

    // ─── Helpers reutilizables ─────────────────────────────────────

    protected function toUtf8(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }
        // Si ya es utf8 válido, no lo toco
        if (mb_check_encoding($value, 'UTF-8') && ! preg_match('/[\xC2-\xDF][\x80-\xBF]/', $value)) {
            return $value;
        }
        return mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
    }

    protected function parseDate(?string $value): ?Carbon
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            Log::channel('etl')->notice('fecha invalida', ['value' => $value, 'table' => $this->legacyTable]);
            return null;
        }
    }

    /** Valida email; devuelve mail en minúscula o null. */
    protected function validEmail(?string $email): ?string
    {
        if (empty($email)) return null;
        $email = strtolower(trim($email));
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    /** Parsea `lat,lng` de `go_googlemaps`/`ho_googlemaps`/`ce_googlemaps`. */
    protected function parseLatLng(?string $value): array
    {
        if (empty($value)) return [null, null];
        $parts = explode(',', str_replace(' ', '', $value));
        if (count($parts) !== 2) return [null, null];
        $lat = (float) $parts[0];
        $lng = (float) $parts[1];
        if ($lat === 0.0 || $lng === 0.0) return [null, null];
        return [$lat, $lng];
    }
}
