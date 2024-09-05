<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Title extends Model
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

    public function characters(): Builder
    {
        return Character::query()
                        ->selectRaw('characters.id, characters.name, characters.link, characters.preview_id, characters.description')
                        ->join('character_comic', 'characters.id', 'character_comic.character_id')
                        ->join('comic_title', 'character_comic.comic_id', 'comic_title.comic_id')
                        ->join('titles', 'titles.id', 'comic_title.title_id')
                        ->where('titles.id', $this->id)
                        ->groupBy('characters.id');
    }

    public function tags(): Builder
    {
        return Tag::query()
                  ->selectRaw('tags.id, tags.name, tags.link')
                  ->join('comic_tag', 'tags.id', 'comic_tag.tag_id')
                  ->join('comic_title', 'comic_tag.comic_id', 'comic_title.comic_id')
                  ->join('titles', 'titles.id', 'comic_title.title_id')
                  ->where('titles.id', $this->id)
                  ->groupBy('tags.id');
    }

    public function authors(): Builder
    {
        return Author::query()
                     ->selectRaw('authors.id, authors.name, authors.link')
                     ->join('comics', 'authors.id', 'comics.author_id')
                     ->join('comic_title', 'comics.id', 'comic_title.comic_id')
                     ->join('titles', 'titles.id', 'comic_title.title_id')
                     ->where('titles.id', $this->id)
                     ->groupBy('authors.id');
    }

    public function definedCharacters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class);
    }

    public function preview(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
