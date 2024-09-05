<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\ComicResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\TitleResource;
use App\Models\Character;
use App\Models\Tag;
use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TitleController extends Controller
{
    public function index()
    {
        return TitleResource::collection(
            Title::query()
                 ->with('comics', fn(BelongsToMany $comics) => $comics->inRandomOrder()->take(1))
                 ->withCount('comics')
                 ->orderByDesc('comics_count')
                 ->orderBy('id')
                 ->paginate(perPage: 16)
        );
    }

    public function show(Title $title)
    {
        return new TitleResource($title);
    }

    public function comics(Title $title)
    {
        return ComicResource::collection(
            $title->comics()
                  ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                  ->withCount('pages')
                  ->orderByDesc('views')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }

    public function characters(Title $title)
    {
        return CharacterResource::collection(
            $title->characters()
                  ->with('comics', fn(BelongsToMany $comics) => $comics
                      ->whereHas('titles', fn(Builder $titles) => $titles
                          ->where('titles.id', $title->id)
                      )
                      ->inRandomOrder()
                      ->take(1)
                  )
                  ->with('definedTitles', fn(BelongsToMany $titles) => $titles->withPivot('role'))
                  ->with('aliases')
                  ->withCount([
                      'comics'        => fn(Builder $comics) => $comics
                          ->whereHas('titles', fn(Builder $titles) => $titles
                              ->where('titles.id', $title->id)
                          ),
                      'definedTitles' => fn(Builder $titles) => $titles->where('titles.id', $title->id)
                  ])
                  ->orderByDesc('defined_titles_count')
                  ->orderByDesc('comics_count')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }

    public function tags(Title $title)
    {
        return TagResource::collection(
            $title->tags()
                  ->with('comics', fn(BelongsToMany $comics) => $comics
                      ->whereHas('titles', fn(Builder $titles) => $titles
                          ->where('titles.id', $title->id)
                      )
                      ->inRandomOrder()
                      ->take(1)
                  )
                  ->withCount([
                      'comics' => fn(Builder $comics) => $comics
                          ->whereHas('titles', fn(Builder $titles) => $titles
                              ->where('titles.id', $title->id)
                          )
                  ])
                  ->orderByDesc('comics_count')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }

    public function authors(Title $title)
    {
        return AuthorResource::collection(
            $title->authors()
                  ->with('comics', fn(HasMany $comics) => $comics
                      ->whereHas('titles', fn(Builder $titles) => $titles
                          ->where('titles.id', $title->id)
                      )
                      ->inRandomOrder()
                      ->take(1)
                  )
                  ->withCount([
                      'comics' => fn(Builder $comics) => $comics
                          ->whereHas('titles', fn(Builder $titles) => $titles
                              ->where('titles.id', $title->id)
                          )
                  ])
                  ->orderByDesc('comics_count')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }

    public function characterComics(Title $title, Character $character)
    {
        return ComicResource::collection(
            $title->comics()
                  ->whereHas('characters', fn(Builder $characters) => $characters->where('characters.id', $character->id))
                  ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                  ->withCount('pages')
                  ->orderByDesc('views')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }

    public function tagComics(Title $title, Tag $tag)
    {
        return ComicResource::collection(
            $title->comics()
                  ->whereHas('tags', fn(Builder $tags) => $tags->where('tags.id', $tag->id))
                  ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                  ->withCount('pages')
                  ->orderByDesc('views')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }
}
