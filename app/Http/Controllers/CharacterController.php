<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\ComicResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\TitleResource;
use App\Models\Character;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CharacterController extends Controller
{
    public function index()
    {
        return CharacterResource::collection(
            Character::query()
                     ->with('comics', fn(BelongsToMany $comics) => $comics->inRandomOrder()->take(1))
                     ->with('aliases', 'definedTitles')
                     ->withCount('comics')
                     ->orderByDesc('comics_count')
                     ->orderBy('id')
                     ->paginate(perPage: 16)
        );
    }

    public function show(Character $character)
    {
        return new CharacterResource($character->load('aliases'));
    }

    public function comics(Character $character)
    {
        return ComicResource::collection(
            $character->comics()
                      ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                      ->withCount('pages')
                      ->orderByDesc('views')
                      ->orderBy('id')
                      ->paginate(perPage: 16)
        );
    }

    public function tags(Character $character)
    {
        return TagResource::collection(
            $character->tags()
                      ->with('comics', fn(BelongsToMany $comics) => $comics
                          ->whereHas('characters', fn(Builder $characters) => $characters
                              ->where('characters.id', $character->id)
                          )
                          ->inRandomOrder()
                          ->take(1)
                      )
                      ->withCount([
                          'comics' => fn(Builder $comics) => $comics
                              ->whereHas('characters', fn(Builder $characters) => $characters
                                  ->where('characters.id', $character->id)
                              )
                      ])
                      ->orderByDesc('comics_count')
                      ->orderBy('id')
                      ->paginate(perPage: 16)
        );
    }

    public function titles(Character $character)
    {
        return TitleResource::collection(
            $character->titles()
                      ->with('comics', fn(BelongsToMany $comics) => $comics
                          ->whereHas('characters', fn(Builder $characters) => $characters
                              ->where('characters.id', $character->id)
                          )
                          ->inRandomOrder()
                          ->take(1)
                      )
                      ->withCount([
                          'comics' => fn(Builder $comics) => $comics
                              ->whereHas('characters', fn(Builder $characters) => $characters
                                  ->where('characters.id', $character->id)
                              )
                      ])
                      ->orderByDesc('comics_count')
                      ->orderBy('id')
                      ->paginate(perPage: 16)
        );
    }

    public function authors(Character $character)
    {
        return AuthorResource::collection(
            $character->authors()
                      ->with('comics', fn(HasMany $comics) => $comics
                          ->whereHas('characters', fn(Builder $characters) => $characters
                              ->where('characters.id', $character->id)
                          )
                          ->inRandomOrder()
                          ->take(1)
                      )
                      ->withCount([
                          'comics' => fn(Builder $comics) => $comics
                              ->whereHas('characters', fn(Builder $characters) => $characters
                                  ->where('characters.id', $character->id)
                              )
                      ])
                      ->orderByDesc('comics_count')
                      ->orderBy('id')
                      ->paginate(perPage: 16)
        );
    }

    public function tagComics(Character $character, Tag $tag)
    {
        return ComicResource::collection(
            $character->comics()
                      ->whereHas('tags', fn(Builder $tags) => $tags->where('tags.id', $tag->id))
                      ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                      ->withCount('pages')
                      ->orderByDesc('views')
                      ->orderBy('id')
                      ->paginate(perPage: 16)
        );
    }
}
