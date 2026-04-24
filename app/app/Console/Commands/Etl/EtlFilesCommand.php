<?php

namespace App\Console\Commands\Etl;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EtlFilesCommand extends Command
{
    protected $signature   = 'etl:files {--source=/legacy_htdocs : Path dentro del contenedor a la raíz htdocs del legacy}';
    protected $description = 'Copia archivos físicos del legacy a storage/app/public/legacy/';

    /**
     * Mapeo: path relativo al htdocs legacy → path destino bajo storage/app/public/legacy/.
     * Los archivos reales viven todos dentro de htdocs/imagenes/, así que la entrada
     * top-level `imagenes` solo copia archivos (no recursivo), y las subcarpetas
     * se copian como entradas separadas al destino que matchea los paths en Media.
     */
    protected array $dirs = [
        // key=path en origen (relativo a --source), value=[dest_rel, recursive]
        'imagenes'               => ['imagenes', false], // solo files sueltos (galería)
        'imagenes/novedades'     => ['novedades', true],
        'imagenes/hospedaje'     => ['hospedaje', true],
        'imagenes/gourmet'       => ['gourmet', true],
        'imagenes/nocturnos'     => ['nocturnos', true],
        'imagenes/alquiler'      => ['alquiler', true],
        'imagenes/clasificados'  => ['clasificados', true],
        'imagenes/recetas'       => ['recetas', true],
        'imagenes/servicios'     => ['servicios', true],
    ];

    public function handle(): int
    {
        $source = rtrim($this->option('source'), '/');
        $dest   = storage_path('app/public/legacy');

        if (!is_dir($source)) {
            $this->error("Source directory no existe: {$source}");
            $this->comment("Montar el htdocs legacy como volumen en docker-compose — ver notas.");
            return self::FAILURE;
        }

        if (!is_dir($dest) && !mkdir($dest, 0755, true)) {
            $this->error("No se pudo crear {$dest}");
            return self::FAILURE;
        }

        $copied  = 0;
        $missing = 0;
        foreach ($this->dirs as $src => [$dst, $recursive]) {
            $srcPath = "{$source}/{$src}";
            $dstPath = "{$dest}/{$dst}";
            if (!is_dir($srcPath)) {
                $this->warn("  skip {$src} (no existe en source)");
                $missing++;
                continue;
            }
            $label = $recursive ? 'recursivo' : 'solo archivos';
            $this->info("  {$src} → {$dst} ({$label})");
            $this->copyDir($srcPath, $dstPath, $recursive, $copied);
        }

        $this->info("Archivos copiados: {$copied} | directorios faltantes: {$missing}");
        Log::channel('etl')->info('etl:files completado', ['copied' => $copied, 'missing_dirs' => $missing]);
        return self::SUCCESS;
    }

    protected function copyDir(string $src, string $dst, bool $recursive, int &$count): void
    {
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        $dir = opendir($src);
        while (false !== ($f = readdir($dir))) {
            if ($f === '.' || $f === '..') continue;
            $s = "{$src}/{$f}";
            $d = "{$dst}/{$f}";
            if (is_dir($s)) {
                if ($recursive) {
                    $this->copyDir($s, $d, true, $count);
                }
                // non-recursive → se saltea directorios
            } else {
                if (!file_exists($d) || filemtime($s) > filemtime($d)) {
                    copy($s, $d);
                    $count++;
                }
            }
        }
        closedir($dir);
    }
}
