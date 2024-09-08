<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    protected $fillable = [
        'title',
        'preview_id',
        'link',
        'views',
        'author_id',
        'video_id',
        'section_id'
    ];

    public function preview(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function hentaiTitle(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'title_id');
    }
}
