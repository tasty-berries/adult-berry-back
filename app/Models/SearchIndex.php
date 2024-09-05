<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;

class SearchIndex extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'searchable_type',
        'searchable_id',
        'extra'
    ];

    protected $casts = [
        'extra' => AsCollection::class
    ];
}
