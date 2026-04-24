<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'last_name', 'email', 'phone',
        'province', 'city', 'extra_data', 'comments', 'ip_address', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'extra_data' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
