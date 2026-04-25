<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Rental extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'places', 'contact_name',
        'phone', 'email', 'address', 'description', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'places' => 'integer',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'places', 'contact_name', 'phone', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $event) => match ($event) {
                'created' => 'creó el alquiler',
                'updated' => 'actualizó el alquiler',
                'deleted' => 'eliminó el alquiler',
                default   => $event,
            });
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
