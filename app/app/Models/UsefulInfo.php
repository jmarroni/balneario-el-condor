<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsefulInfo extends Model
{
    use HasFactory;

    protected $table = 'useful_info';

    protected $fillable = [
        'title', 'phone', 'website', 'email', 'address',
        'latitude', 'longitude', 'sort_order', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
            'sort_order' => 'integer',
        ];
    }
}
