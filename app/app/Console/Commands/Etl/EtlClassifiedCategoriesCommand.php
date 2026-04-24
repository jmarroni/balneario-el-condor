<?php

namespace App\Console\Commands\Etl;

use App\Models\ClassifiedCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EtlClassifiedCategoriesCommand extends Command
{
    protected $signature   = 'etl:classified-categories';
    protected $description = 'Extrae categorías únicas de clasificados legacy → classified_categories';

    public function handle(): int
    {
        $rows = DB::connection('legacy')
            ->table('clasificados')
            ->select('cla_categoria')
            ->whereNotNull('cla_categoria')
            ->where('cla_categoria', '!=', '')
            ->distinct()
            ->get();

        $created = 0;
        foreach ($rows as $r) {
            $name = trim(mb_convert_encoding($r->cla_categoria, 'UTF-8', 'ISO-8859-1'));
            if ($name === '') continue;

            ClassifiedCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name],
            );
            $created++;
        }

        $this->info("categorias clasificados: {$created}");
        return self::SUCCESS;
    }
}
