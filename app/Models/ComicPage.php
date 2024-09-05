<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComicPage extends Model
{
    protected $fillable = [
        'comic_id',
        'image_id'
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
