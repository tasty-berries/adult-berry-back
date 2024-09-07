<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'link'
    ];

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }
}
