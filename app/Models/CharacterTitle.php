<?php

namespace App\Models;

use App\Enums\CharacterTitleRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CharacterTitle extends Pivot
{
    protected $casts = [
        'role' => CharacterTitleRole::class
    ];
}
