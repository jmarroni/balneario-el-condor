<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Galería pública con filtro por año (basado en taken_on).
     * El detalle de cada imagen vive en un lightbox Alpine inline,
     * por lo que no hay método show().
     */
    public function index(Request $request): View
    {
        $base = GalleryImage::query()
            ->whereNotNull('path')
            ->orderByDesc('taken_on')
            ->orderByDesc('id');

        $year = $request->integer('anio') ?: null;

        if ($year !== null) {
            $base->whereYear('taken_on', $year);
        }

        // Años disponibles con conteos (para los pills).
        $years = GalleryImage::query()
            ->whereNotNull('taken_on')
            ->selectRaw('YEAR(taken_on) as yr, COUNT(*) as total')
            ->groupBy('yr')
            ->orderByDesc('yr')
            ->pluck('total', 'yr')
            ->toArray();

        return view('public.galeria.index', [
            'images'     => $base->paginate(48)->withQueryString(),
            'years'      => $years,
            'current'    => $year,
            'totalCount' => GalleryImage::count(),
        ]);
    }
}
