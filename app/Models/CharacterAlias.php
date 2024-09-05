<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterAlias extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'character_id',
        'name',
        'link'
    ];

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }
}
