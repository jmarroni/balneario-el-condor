<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'last_name', 'email', 'message', 'zone', 'read', 'legacy_id',
    ];

    protected function casts(): array
    {
        return ['read' => 'boolean'];
    }
}
