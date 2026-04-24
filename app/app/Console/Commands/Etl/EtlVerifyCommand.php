<?php

namespace App\Console\Commands\Etl;

use App\Models\{
    AdvertisingContact, Classified, ClassifiedContact, Event, EventRegistration,
    GalleryImage, Lodging, Media, NearbyPlace, News, NewsCategory,
    NewsletterSubscriber, Recipe, Rental, ServiceProvider, SurveyResponse,
    Tide, UsefulInfo, User, Venue,
};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class EtlVerifyCommand extends Command
{
    protected $signature   = 'etl:verify';
    protected $description = 'Reporta conteos legacy vs nuevo y files faltantes';

    public function handle(): int
    {
        $rows = [];
        $checks = [
            ['usuarios',           'users (legacy_id)',  fn() => User::whereNotNull('legacy_id')->count()],
            ['categorias_novedades', 'news_categories',   fn() => NewsCategory::whereNotNull('legacy_id')->count()],
            ['novedades',          'news',                fn() => News::whereNotNull('legacy_id')->count()],
            ['agenda',             'events (legacy)',     fn() => Event::whereNotNull('legacy_id')->count()],
            ['tejofiesta',         'event_regs (tejo-)',  fn() => EventRegistration::where('legacy_id', 'like', 'tejo-%')->count()],
            ['primavera',          'event_regs (prim-)',  fn() => EventRegistration::where('legacy_id', 'like', 'primavera-%')->count()],
            ['hospedaje',          'lodgings',            fn() => Lodging::whereNotNull('legacy_id')->count()],
            ['gourmet',            'venues gourmet',      fn() => Venue::where('category', 'gourmet')->count()],
            ['nocturnos',          'venues nightlife',    fn() => Venue::where('category', 'nightlife')->count()],
            ['alquiler',           'rentals',             fn() => Rental::whereNotNull('legacy_id')->count()],
            ['clasificados',       'classifieds',         fn() => Classified::whereNotNull('legacy_id')->count()],
            ['clasificados_mail',  'classified_contacts', fn() => ClassifiedContact::whereNotNull('legacy_id')->count()],
            ['recetas',            'recipes',             fn() => Recipe::whereNotNull('legacy_id')->count()],
            ['servicios',          'service_providers',   fn() => ServiceProvider::whereNotNull('legacy_id')->count()],
            ['imagenes',           'gallery_images',      fn() => GalleryImage::count()],
            ['cercanos',           'nearby_places',       fn() => NearbyPlace::count()],
            ['informacionutil',    'useful_info',         fn() => UsefulInfo::count()],
            ['mareas',             'tides',               fn() => Tide::count()],
            ['newsletter',         'newsletter_subs',     fn() => NewsletterSubscriber::count()],
            ['encuesta',           'survey_responses',    fn() => SurveyResponse::count()],
            ['publicite',          'advertising_contacts', fn() => AdvertisingContact::count()],
        ];

        foreach ($checks as [$legacy, $target, $newFn]) {
            $legacyCount = DB::connection('legacy')->table($legacy)->count();
            $newCount    = $newFn();
            $diff        = $legacyCount - $newCount;
            $status      = $diff === 0 ? 'OK' : ($diff > 0 ? "FALTAN {$diff}" : "SOBRAN " . abs($diff));
            $rows[]      = [$legacy, $target, $legacyCount, $newCount, $status];
        }

        $this->table(['Legacy', 'Nuevo', 'Legacy count', 'Nuevo count', 'Estado'], $rows);

        // Chequear files referenciados vs físicos
        $this->checkMissingFiles();

        return self::SUCCESS;
    }

    protected function checkMissingFiles(): void
    {
        $missing = [];
        $checked = 0;
        Media::select('path')->chunk(500, function ($batch) use (&$missing, &$checked) {
            foreach ($batch as $m) {
                $checked++;
                $full = storage_path('app/public/' . $m->path);
                if (!File::exists($full)) {
                    $missing[] = $m->path;
                }
            }
        });

        $this->info("archivos chequeados: {$checked} | faltantes: " . count($missing));
        if (count($missing) > 0) {
            $logPath = storage_path('logs/etl-missing-files.log');
            File::put($logPath, implode("\n", $missing));
            $this->warn("listado escrito a: {$logPath}");
        }
    }
}
