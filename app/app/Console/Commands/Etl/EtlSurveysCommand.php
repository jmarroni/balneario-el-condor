<?php

namespace App\Console\Commands\Etl;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EtlSurveysCommand extends Command
{
    protected $signature   = 'etl:surveys';
    protected $description = 'Crea survey seed + migra encuesta → survey_responses';

    protected int $processed = 0;
    protected int $skipped   = 0;
    protected int $errors    = 0;

    public function handle(): int
    {
        $this->info('[etl:surveys] Creando survey seed y migrando respuestas legacy encuesta → survey_responses');

        $survey = Survey::updateOrCreate(
            ['title' => 'Encuesta legacy'],
            [
                'question' => 'Encuesta migrada desde el sitio anterior (pregunta original no preservada)',
                'options'  => [
                    ['key' => 1, 'label' => 'Opción 1'],
                    ['key' => 2, 'label' => 'Opción 2'],
                    ['key' => 3, 'label' => 'Opción 3'],
                    ['key' => 4, 'label' => 'Opción 4'],
                ],
                'active'   => false,
            ],
        );

        $total = DB::connection('legacy')->table('encuesta')->count();
        $bar   = $this->output->createProgressBar($total);

        DB::connection('legacy')->table('encuesta')
            ->orderBy('en_id')
            ->chunk(500, function ($rows) use ($survey, $bar) {
                foreach ($rows as $r) {
                    try {
                        SurveyResponse::updateOrCreate(
                            ['legacy_id' => $r->en_id],
                            [
                                'survey_id'      => $survey->id,
                                'option_key'     => (int) $r->en_opcion,
                                'comment'        => $this->toUtf8($r->en_comentario),
                                'email'          => $this->validEmail($r->en_mail),
                                'accepted_terms' => (bool) $r->en_acepto,
                                'ip_address'     => $r->en_ip,
                                'created_at'     => $this->parseDate($r->en_fecha),
                            ],
                        );
                        $this->processed++;
                    } catch (\Throwable $e) {
                        $this->errors++;
                        Log::channel('etl')->error('etl:surveys row error', [
                            'en_id'   => $r->en_id ?? null,
                            'message' => $e->getMessage(),
                        ]);
                    }
                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->line("  procesadas: {$this->processed} | saltadas: {$this->skipped} | errores: {$this->errors}");

        return self::SUCCESS;
    }

    protected function toUtf8(?string $v): ?string
    {
        if ($v === null || $v === '') {
            return $v;
        }
        if (mb_check_encoding($v, 'UTF-8') && ! preg_match('/[\xC2-\xDF][\x80-\xBF]/', $v)) {
            return $v;
        }
        return mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1');
    }

    protected function validEmail(?string $e): ?string
    {
        if (empty($e)) {
            return null;
        }
        $e = strtolower(trim($e));
        return filter_var($e, FILTER_VALIDATE_EMAIL) ? $e : null;
    }

    protected function parseDate(?string $v): ?Carbon
    {
        if (empty($v) || $v === '0000-00-00' || $v === '0000-00-00 00:00:00') {
            return null;
        }
        try {
            return Carbon::parse($v);
        } catch (\Throwable) {
            return null;
        }
    }
}
