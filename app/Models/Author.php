<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'link'
    ];

    public function comics(): HasMany
    {
        return $this->hasMany(Comic::class);
    }

    public function characters(): Builder
    {
        return Character::query()
                        ->selectRaw('characters.id, characters.name, characters.link, characters.description, characters.preview_id')
                        ->join('character_comic', 'characters.id', 'character_comic.character_id')
                        ->join('comics', 'character_comic.comic_id', 'comics.id')
                        ->join('authors', 'authors.id', 'comics.author_id')
                        ->where('authors.id', $this->id)
                        ->groupBy('characters.id');
    }

    public function tags(): Builder
    {
        return Tag::query()
                  ->selectRaw('tags.id, tags.name, tags.link')
                  ->join('comic_tag', 'tags.id', 'comic_tag.tag_id')
                  ->join('comics', 'comic_tag.comic_id', 'comics.id')
                  ->join('authors', 'authors.id', 'comics.author_id')
                  ->where('authors.id', $this->id)
                  ->groupBy('tags.id');
    }

    public function titles(): Builder
    {
        return Title::query()
                    ->selectRaw('titles.id, titles.name, titles.link, titles.description, titles.preview_id')
                    ->join('comic_title', 'titles.id', 'comic_title.title_id')
                    ->join('comics', 'comic_title.comic_id', 'comics.id')
                    ->join('authors', 'authors.id', 'comics.author_id')
                    ->where('authors.id', $this->id)
                    ->groupBy('titles.id');
    }
}
