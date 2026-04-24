<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tide extends Model
{
    use HasFactory;

    protected $fillable = [
        'location', 'date',
        'first_high', 'first_high_height',
        'first_low', 'first_low_height',
        'second_high', 'second_high_height',
        'second_low', 'second_low_height',
        'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
