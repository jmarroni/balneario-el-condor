<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug',
        'prep_minutes', 'cook_minutes', 'servings', 'cost',
        'ingredients', 'instructions', 'author',
        'published_on', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'prep_minutes' => 'integer',
            'cook_minutes' => 'integer',
            'published_on' => 'date',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
