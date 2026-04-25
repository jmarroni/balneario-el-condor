<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Event extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'location',
        'starts_at', 'ends_at', 'all_day', 'featured',
        'accepts_registrations', 'sort_order', 'external_url', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'              => 'datetime',
            'ends_at'                => 'datetime',
            'all_day'                => 'boolean',
            'featured'               => 'boolean',
            'accepts_registrations'  => 'boolean',
            'sort_order'             => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title', 'slug', 'description', 'location',
                'starts_at', 'ends_at', 'accepts_registrations', 'featured',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => match ($event) {
                'created' => 'creó el evento',
                'updated' => 'actualizó el evento',
                'deleted' => 'eliminó el evento',
                default   => $event,
            });
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function getIsPastAttribute(): bool
    {
        return $this->starts_at !== null && $this->starts_at->isPast();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->starts_at === null || $this->starts_at->isFuture();
    }
}
