<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classified extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'classified_category_id', 'title', 'slug', 'description',
        'contact_name', 'contact_email', 'address', 'latitude', 'longitude',
        'video_url', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'views'     => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassifiedCategory::class, 'classified_category_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClassifiedContact::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
