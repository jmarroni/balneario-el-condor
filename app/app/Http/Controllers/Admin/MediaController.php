<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMediaRequest;
use App\Models\Media;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    use AuthorizesRequests;

    /**
     * Ancho máximo (px) al que se reescalan las imágenes subidas.
     */
    private const MAX_WIDTH = 1200;

    public function store(StoreMediaRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var class-string $type */
        $type = $data['mediable_type'];
        $id = (int) $data['mediable_id'];

        if (! class_exists($type)) {
            throw ValidationException::withMessages([
                'mediable_type' => 'Tipo de modelo inválido.',
            ]);
        }

        /** @var \Illuminate\Database\Eloquent\Model|null $mediable */
        $mediable = $type::query()->find($id);
        if ($mediable === null) {
            throw ValidationException::withMessages([
                'mediable_id' => 'Modelo no encontrado.',
            ]);
        }

        /** @var UploadedFile $file */
        $file = $request->file('file');

        $path = $this->storeImage($file);

        $nextOrder = (int) Media::query()
            ->where('mediable_type', $type)
            ->where('mediable_id', $id)
            ->max('sort_order');
        $nextOrder = Media::query()
            ->where('mediable_type', $type)
            ->where('mediable_id', $id)
            ->exists() ? $nextOrder + 1 : 0;

        $media = Media::create([
            'mediable_type' => $type,
            'mediable_id'   => $id,
            'path'          => $path,
            'alt'           => $data['alt'] ?? null,
            'type'          => 'image',
            'sort_order'    => $nextOrder,
        ]);

        return response()->json([
            'id'         => $media->id,
            'url'        => Storage::disk('public')->url($path),
            'alt'        => $media->alt,
            'sort_order' => $media->sort_order,
        ], 201);
    }

    public function destroy(Media $media): JsonResponse
    {
        $mediable = $this->resolveMediable($media);
        $this->authorize('update', $mediable);

        if (Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return response()->json(['ok' => true]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items'                => ['required', 'array'],
            'items.*.id'           => ['required', 'integer'],
            'items.*.sort_order'   => ['required', 'integer', 'min:0'],
        ]);

        $ids = array_column($validated['items'], 'id');
        /** @var \Illuminate\Database\Eloquent\Collection<int, Media> $mediaItems */
        $mediaItems = Media::query()->whereIn('id', $ids)->get()->keyBy('id');

        // Autorizar cada mediable antes de tocar nada.
        foreach ($mediaItems as $media) {
            $mediable = $this->resolveMediable($media);
            $this->authorize('update', $mediable);
        }

        foreach ($validated['items'] as $item) {
            $media = $mediaItems->get($item['id']);
            if ($media !== null) {
                $media->update(['sort_order' => $item['sort_order']]);
            }
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Persiste el upload en disco público, generando una versión recortada
     * a MAX_WIDTH si el original es más ancho.
     */
    private function storeImage(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        if ($ext === '') {
            $ext = 'jpg';
        }
        $folder = 'media/' . now()->format('Y/m');
        $filename = (string) Str::ulid() . '.' . $ext;
        $relativePath = $folder . '/' . $filename;
        $absolutePath = Storage::disk('public')->path($relativePath);

        Storage::disk('public')->makeDirectory($folder);

        $resized = $this->resizeIfNeeded($file->getRealPath(), $absolutePath, $ext);
        if (! $resized) {
            $file->move(dirname($absolutePath), basename($absolutePath));
        }

        return $relativePath;
    }

    /**
     * Intenta reescalar la imagen a MAX_WIDTH usando GD. Si el original es
     * más chico o la extensión no es soportada, devuelve false para que el
     * caller mueva el archivo original.
     */
    private function resizeIfNeeded(string $sourcePath, string $targetPath, string $ext): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $info = @getimagesize($sourcePath);
        if ($info === false) {
            return false;
        }

        [$width, $height] = $info;
        if ($width <= self::MAX_WIDTH) {
            return false;
        }

        $src = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($sourcePath),
            'png'         => @imagecreatefrompng($sourcePath),
            'gif'         => @imagecreatefromgif($sourcePath),
            'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default       => false,
        };

        if ($src === false || $src === null) {
            return false;
        }

        $newWidth = self::MAX_WIDTH;
        $newHeight = (int) round($height * ($newWidth / $width));
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // Preservar transparencia para PNG/GIF/WebP.
        if (in_array($ext, ['png', 'gif', 'webp'], true)) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $ok = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($dst, $targetPath, 85),
            'png'         => imagepng($dst, $targetPath, 6),
            'gif'         => imagegif($dst, $targetPath),
            'webp'        => function_exists('imagewebp') ? imagewebp($dst, $targetPath, 85) : false,
            default       => false,
        };

        imagedestroy($src);
        imagedestroy($dst);

        return (bool) $ok;
    }

    /**
     * Carga el mediable asociado al Media para autorizar contra su policy.
     */
    private function resolveMediable(Media $media): \Illuminate\Database\Eloquent\Model
    {
        /** @var class-string $type */
        $type = $media->mediable_type;
        if (! class_exists($type)) {
            abort(404);
        }
        /** @var \Illuminate\Database\Eloquent\Model|null $mediable */
        $mediable = $type::query()->find($media->mediable_id);
        if ($mediable === null) {
            abort(404);
        }

        return $mediable;
    }
}
