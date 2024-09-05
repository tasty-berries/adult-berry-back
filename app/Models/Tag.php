<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'link'
    ];

    public function comics(): BelongsToMany
    {
        return $this->belongsToMany(Comic::class);
    }

    public function characters(): Builder
    {
        return Character::query()
                        ->selectRaw('characters.id, characters.name, characters.link, characters.description, characters.preview_id')
                        ->join('character_comic', 'characters.id', 'character_comic.character_id')
                        ->join('comic_tag', 'character_comic.comic_id', 'comic_tag.comic_id')
                        ->join('tags', 'tags.id', 'comic_tag.tag_id')
                        ->where('tags.id', $this->id)
                        ->groupBy('characters.id');
    }

    public function titles(): Builder
    {
        return Title::query()
                    ->selectRaw('titles.id, titles.name, titles.link, titles.description, titles.preview_id')
                    ->join('comic_title', 'titles.id', 'comic_title.title_id')
                    ->join('comic_tag', 'comic_title.comic_id', 'comic_tag.comic_id')
                    ->join('tags', 'tags.id', 'comic_tag.tag_id')
                    ->where('tags.id', $this->id)
                    ->groupBy('titles.id');
    }

    public function authors(): Builder
    {
        return Author::query()
                     ->selectRaw('authors.id, authors.name, authors.link')
                     ->join('comics', 'authors.id', 'comics.author_id')
                     ->join('comic_tag', 'comics.id', 'comic_tag.comic_id')
                     ->join('tags', 'tags.id', 'comic_tag.tag_id')
                     ->where('tags.id', $this->id)
                     ->groupBy('authors.id');
    }
}
