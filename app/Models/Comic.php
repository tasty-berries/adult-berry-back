<?php

namespace App\Models;

use App\Models\Scopes\LimitContentScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ScopedBy(LimitContentScope::class)]
class Comic extends Model
{
    protected $fillable = [
        'title',
        'link',
        'views',
        'preview_id'
    ];

    public function preview(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class);
    }

    public function titles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(ComicPage::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
