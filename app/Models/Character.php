<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Character extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'link',
        'description',
        'preview_id'
    ];

    public function comics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class);
    }

    public function tags(): Builder
    {
        return Tag::query()
                  ->selectRaw('tags.id, tags.name, tags.link')
                  ->join('comic_tag', 'tags.id', 'comic_tag.tag_id')
                  ->join('character_comic', 'comic_tag.comic_id', 'character_comic.comic_id')
                  ->join('characters', 'characters.id', 'character_comic.character_id')
                  ->where('characters.id', $this->id)
                  ->groupBy('tags.id');
    }

    public function titles(): Builder
    {
        return Title::query()
                    ->selectRaw('titles.id, titles.name, titles.link, titles.description, titles.preview_id')
                    ->join('comic_title', 'titles.id', 'comic_title.title_id')
                    ->join('character_comic', 'comic_title.comic_id', 'character_comic.comic_id')
                    ->join('characters', 'characters.id', 'character_comic.character_id')
                    ->where('characters.id', $this->id)
                    ->groupBy('titles.id');
    }

    public function authors(): Builder
    {
        return Author::query()
                     ->selectRaw('authors.id, authors.name, authors.link')
                     ->join('comics', 'authors.id', 'comics.author_id')
                     ->join('character_comic', 'comics.id', 'character_comic.comic_id')
                     ->join('characters', 'characters.id', 'character_comic.character_id')
                     ->where('characters.id', $this->id)
                     ->groupBy('authors.id');
    }

    public function preview(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    public function definedTitles(): BelongsToMany
    {
        return $this->belongsToMany(Title::class);
    }

    public function aliases(): HasMany
    {
        return $this->hasMany(CharacterAlias::class);
    }
}
