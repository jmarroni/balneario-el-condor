<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description',
        'path', 'thumb_path', 'original_path',
        'taken_on', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'taken_on' => 'date',
            'views'    => 'integer',
        ];
    }
}
