<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Classified;
use App\Models\Event;
use App\Models\Lodging;
use App\Models\NearbyPlace;
use App\Models\News;
use App\Models\Page;
use App\Models\Recipe;
use App\Models\Rental;
use App\Models\ServiceProvider;
use App\Models\Venue;
use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    /**
     * Map de mediable_type (class FQN) al prefijo de permiso correspondiente.
     *
     * @var array<class-string, string>
     */
    public const PERMISSION_PREFIXES = [
        News::class            => 'news',
        Event::class           => 'events',
        Lodging::class         => 'lodgings',
        Venue::class           => 'venues',
        Rental::class          => 'rentals',
        Classified::class      => 'classifieds',
        Recipe::class          => 'recipes',
        ServiceProvider::class => 'service-providers',
        Page::class            => 'pages',
        NearbyPlace::class     => 'nearby-places',
    ];

    public function authorize(): bool
    {
        $type = (string) $this->input('mediable_type', '');
        $prefix = self::PERMISSION_PREFIXES[$type] ?? null;

        if ($prefix === null) {
            return false;
        }

        return $this->user()?->can("{$prefix}.update") ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'file'          => ['required', 'image', 'max:5120'],
            'mediable_type' => ['required', 'string'],
            'mediable_id'   => ['required', 'integer'],
            'alt'           => ['nullable', 'string', 'max:255'],
        ];
    }
}
