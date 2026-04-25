<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'news_category_id',
        'title',
        'slug',
        'body',
        'video_url',
        'published_at',
        'views',
        'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'body', 'published_at', 'news_category_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => match ($event) {
                'created' => 'creó la noticia',
                'updated' => 'actualizó la noticia',
                'deleted' => 'eliminó la noticia',
                default   => $event,
            });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    /**
     * Resumen plano del cuerpo (140 chars). Sin field dedicado en la tabla:
     * lo derivamos del body limpio para que tarjetas y meta tags lo consuman.
     */
    public function getExcerptAttribute(): string
    {
        return Str::limit(trim(strip_tags((string) $this->body)), 140);
    }

    /**
     * Estimación de lectura (220 palabras/minuto). Mínimo 1 minuto.
     */
    public function getReadingMinutesAttribute(): int
    {
        $words = str_word_count(strip_tags((string) $this->body));

        return max(1, (int) ceil($words / 220));
    }
}
