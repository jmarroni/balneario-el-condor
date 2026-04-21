<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

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

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
