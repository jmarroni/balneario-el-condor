<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGalleryImageRequest;
use App\Http\Requests\Admin\UpdateGalleryImageRequest;
use App\Models\GalleryImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GalleryImageController extends Controller
{
    use AuthorizesRequests;

    private const THUMB_SIZE = 120;

    private const DISK = 'public';

    private const DIR = 'gallery';

    private const THUMB_DIR = 'gallery/thumbs';

    public function index(): View
    {
        $this->authorize('viewAny', GalleryImage::class);

        $images = GalleryImage::query()
            ->latest('id')
            ->paginate(20);

        return view('admin.gallery.index', compact('images'));
    }

    public function create(): View
    {
        $this->authorize('create', GalleryImage::class);

        return view('admin.gallery.create', [
            'galleryImage' => new GalleryImage,
        ]);
    }

    public function store(StoreGalleryImageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = ! empty($data['slug'])
            ? $data['slug']
            : Str::slug($data['title'] ?? (string) Str::ulid());

        /** @var UploadedFile $file */
        $file = $request->file('image');
        $paths = $this->storeImage($file);

        $data['path'] = $paths['path'];
        $data['thumb_path'] = $paths['thumb_path'];
        $data['original_path'] = $paths['original_path'];

        unset($data['image']);

        $image = GalleryImage::create($data);

        return redirect()
            ->route('admin.gallery.edit', $image)
            ->with('success', 'Imagen creada.');
    }

    public function show(GalleryImage $galleryImage): RedirectResponse
    {
        $this->authorize('view', $galleryImage);

        return redirect()->route('admin.gallery.edit', $galleryImage);
    }

    public function edit(GalleryImage $galleryImage): View
    {
        $this->authorize('update', $galleryImage);

        return view('admin.gallery.edit', compact('galleryImage'));
    }

    public function update(UpdateGalleryImageRequest $request, GalleryImage $galleryImage): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? $galleryImage->slug);
        }

        if ($request->hasFile('image')) {
            /** @var UploadedFile $file */
            $file = $request->file('image');
            $paths = $this->storeImage($file);

            $this->deleteFiles([
                $galleryImage->path,
                $galleryImage->thumb_path,
                $galleryImage->original_path,
            ]);

            $data['path'] = $paths['path'];
            $data['thumb_path'] = $paths['thumb_path'];
            $data['original_path'] = $paths['original_path'];
        }

        unset($data['image']);

        $galleryImage->update($data);

        return redirect()
            ->route('admin.gallery.edit', $galleryImage)
            ->with('success', 'Imagen actualizada.');
    }

    public function destroy(GalleryImage $galleryImage): RedirectResponse
    {
        $this->authorize('delete', $galleryImage);

        $this->deleteFiles([
            $galleryImage->path,
            $galleryImage->thumb_path,
            $galleryImage->original_path,
        ]);

        $galleryImage->delete();

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', 'Imagen eliminada.');
    }

    /**
     * Guarda el archivo y genera un thumbnail.
     *
     * @return array{path: string, thumb_path: string, original_path: string}
     */
    private function storeImage(UploadedFile $file): array
    {
        $ulid = (string) Str::ulid();
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');

        $relativePath = self::DIR.'/'.$ulid.'.'.$ext;
        $thumbRelativePath = self::THUMB_DIR.'/'.$ulid.'.'.$ext;

        Storage::disk(self::DISK)->putFileAs(self::DIR, $file, $ulid.'.'.$ext);

        // Generar thumb
        $this->generateThumb($file, $thumbRelativePath, $relativePath);

        return [
            'path' => $relativePath,
            'thumb_path' => $thumbRelativePath,
            'original_path' => $relativePath,
        ];
    }

    /**
     * Genera un thumbnail cuadrado usando GD. Si GD falla, copia el original.
     */
    private function generateThumb(UploadedFile $source, string $thumbRelativePath, string $fallbackRelativePath): void
    {
        $disk = Storage::disk(self::DISK);

        // Asegurar directorio de thumbs
        if (! $disk->exists(self::THUMB_DIR)) {
            $disk->makeDirectory(self::THUMB_DIR);
        }

        $mime = $source->getMimeType();
        $thumbData = null;

        if (extension_loaded('gd')) {
            try {
                $sourceImg = $this->createImageFromMime($source->getRealPath(), $mime);
                if ($sourceImg !== null) {
                    $thumb = $this->makeSquareThumb($sourceImg, self::THUMB_SIZE);
                    $thumbData = $this->encodeImage($thumb, $mime);
                    imagedestroy($sourceImg);
                    imagedestroy($thumb);
                }
            } catch (\Throwable $e) {
                // Fallback a copia
                $thumbData = null;
            }
        }

        if ($thumbData !== null) {
            $disk->put($thumbRelativePath, $thumbData);
        } else {
            // Fallback: copiar el original como thumb
            if ($disk->exists($fallbackRelativePath)) {
                $disk->put($thumbRelativePath, $disk->get($fallbackRelativePath));
            } else {
                $disk->put($thumbRelativePath, file_get_contents($source->getRealPath()));
            }
        }
    }

    /**
     * @return \GdImage|null
     */
    private function createImageFromMime(string $path, ?string $mime)
    {
        $img = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $img === false ? null : $img;
    }

    private function makeSquareThumb(\GdImage $src, int $size): \GdImage
    {
        $srcW = imagesx($src);
        $srcH = imagesy($src);
        $side = min($srcW, $srcH);
        $srcX = (int) (($srcW - $side) / 2);
        $srcY = (int) (($srcH - $side) / 2);

        $thumb = imagecreatetruecolor($size, $size);
        imagecopyresampled($thumb, $src, 0, 0, $srcX, $srcY, $size, $size, $side, $side);

        return $thumb;
    }

    private function encodeImage(\GdImage $img, ?string $mime): ?string
    {
        ob_start();
        $ok = match ($mime) {
            'image/png' => imagepng($img),
            'image/gif' => imagegif($img),
            'image/webp' => function_exists('imagewebp') ? imagewebp($img) : imagejpeg($img, null, 85),
            default => imagejpeg($img, null, 85),
        };
        $data = ob_get_clean();

        return $ok ? $data : null;
    }

    /**
     * @param  array<int, string|null>  $paths
     */
    private function deleteFiles(array $paths): void
    {
        $disk = Storage::disk(self::DISK);
        foreach (array_unique(array_filter($paths)) as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }
}
