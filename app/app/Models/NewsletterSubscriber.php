<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'status', 'confirmation_token',
        'subscribed_at', 'confirmed_at', 'unsubscribed_at',
        'ip_address', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at'   => 'datetime',
            'confirmed_at'    => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
