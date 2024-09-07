<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\ComicResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\TitleResource;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TagController extends Controller
{
    public function index()
    {
        return TagResource::collection(
            Tag::query()
               ->has('comics')
               ->with('comics', fn(BelongsToMany $comics) => $comics->inRandomOrder()->take(1))
               ->withCount('comics')
               ->orderByDesc('comics_count')
               ->orderBy('id')
               ->paginate(perPage: 16)
        );
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    public function comics(Tag $tag)
    {
        return ComicResource::collection(
            $tag->comics()
                ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                ->withCount('pages')
                ->orderByDesc('views')
                ->orderBy('id')
                ->paginate(perPage: 16)
        );
    }

    public function characters(Tag $tag)
    {
        return CharacterResource::collection(
            $tag->characters()
                ->whereHas('comics', fn(Builder $has) => $has
                    ->whereHas('tags', fn(Builder $characters) => $characters
                        ->where('tags.id', $tag->id)
                    )
                )
                ->with('comics', fn(BelongsToMany $comics) => $comics
                    ->whereHas('tags', fn(Builder $tags) => $tags
                        ->where('tags.id', $tag->id)
                    )
                    ->inRandomOrder()
                    ->take(1)
                )
                ->withCount([
                    'comics' => fn(Builder $comics) => $comics
                        ->whereHas('tags', fn(Builder $tags) => $tags
                            ->where('tags.id', $tag->id)
                        )
                ])
                ->orderByDesc('comics_count')
                ->orderBy('id')
                ->paginate(perPage: 16)
        );
    }

    public function titles(Tag $tag)
    {
        return TitleResource::collection(
            $tag->titles()
                ->whereHas('comics', fn(Builder $has) => $has
                    ->whereHas('tags', fn(Builder $characters) => $characters
                        ->where('tags.id', $tag->id)
                    )
                )
                ->with('comics', fn(BelongsToMany $comics) => $comics
                    ->whereHas('tags', fn(Builder $characters) => $characters
                        ->where('tags.id', $tag->id)
                    )
                    ->inRandomOrder()
                    ->take(1)
                )
                ->withCount([
                    'comics' => fn(Builder $comics) => $comics
                        ->whereHas('tags', fn(Builder $tags) => $tags
                            ->where('tags.id', $tag->id)
                        )
                ])
                ->orderByDesc('comics_count')
                ->orderBy('id')
                ->paginate(perPage: 16)
        );
    }

    public function authors(Tag $tag)
    {
        return AuthorResource::collection(
            $tag->authors()
                ->whereHas('comics', fn(Builder $has) => $has
                    ->whereHas('tags', fn(Builder $characters) => $characters
                        ->where('tags.id', $tag->id)
                    )
                )
                ->with('comics', fn(HasMany $comics) => $comics
                    ->whereHas('tags', fn(Builder $characters) => $characters
                        ->where('tags.id', $tag->id)
                    )
                    ->inRandomOrder()
                    ->take(1)
                )
                ->withCount([
                    'comics' => fn(Builder $comics) => $comics
                        ->whereHas('tags', fn(Builder $tags) => $tags
                            ->where('tags.id', $tag->id)
                        )
                ])
                ->orderByDesc('comics_count')
                ->orderBy('id')
                ->paginate(perPage: 16)
        );
    }
}
