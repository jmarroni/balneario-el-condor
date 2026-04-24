<?php

namespace App\Console\Commands\Etl;

use Illuminate\Console\Command;

class EtlAllCommand extends Command
{
    protected $signature   = 'etl:all {--skip-files : No copiar archivos físicos al final}';
    protected $description = 'Ejecuta todo el ETL legacy → nuevo en orden';

    /** @var string[] Orden estricto. */
    protected array $pipeline = [
        'etl:users',
        'etl:news-categories',
        'etl:news',
        'etl:events',
        'etl:event-regs',
        'etl:lodgings',
        'etl:venues',
        'etl:rentals',
        'etl:classified-categories',
        'etl:classifieds',
        'etl:classified-contacts',
        'etl:recipes',
        'etl:service-providers',
        'etl:gallery',
        'etl:nearby',
        'etl:useful-info',
        'etl:tides',
        'etl:newsletter',
        'etl:surveys',
        'etl:ad-contacts',
    ];

    public function handle(): int
    {
        $start = now();
        foreach ($this->pipeline as $cmd) {
            $this->info("▶ {$cmd}");
            $code = $this->call($cmd);
            if ($code !== self::SUCCESS) {
                $this->error("✖ {$cmd} terminó con código {$code} — abortando pipeline");
                return self::FAILURE;
            }
        }

        if (! $this->option('skip-files')) {
            $this->info('▶ etl:files');
            $this->call('etl:files');
        }

        $this->info('▶ etl:verify');
        $this->call('etl:verify');

        $this->info("✓ ETL completo en " . $start->diffForHumans(null, true));
        return self::SUCCESS;
    }
}
