<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by_user_id', 'subject', 'body_html', 'body_text',
        'status', 'scheduled_at', 'sent_at', 'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at'      => 'datetime',
            'sent_count'   => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
