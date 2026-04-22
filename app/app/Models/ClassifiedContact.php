<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassifiedContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'classified_id', 'contact_name', 'contact_email', 'contact_phone',
        'message', 'destination_email', 'ip_address', 'legacy_id',
    ];

    public function classified(): BelongsTo
    {
        return $this->belongsTo(Classified::class);
    }
}
