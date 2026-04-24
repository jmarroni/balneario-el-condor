<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id', 'option_key', 'comment',
        'email', 'accepted_terms', 'ip_address', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'option_key'     => 'integer',
            'accepted_terms' => 'boolean',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}
