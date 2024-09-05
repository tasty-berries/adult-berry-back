<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthorResource;
use App\Http\Resources\CharacterResource;
use App\Http\Resources\ComicResource;
use App\Http\Resources\TagResource;
use App\Http\Resources\TitleResource;
use App\Models\Author;
use App\Models\Character;
use App\Models\Tag;
use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuthorController extends Controller
{
    public function index()
    {
        return AuthorResource::collection(
            Author::query()
                  ->with('comics', fn(HasMany $comics) => $comics->inRandomOrder()->take(1))
                  ->withCount('comics')
                  ->withSum('comics', 'views')
                  ->orderByDesc('comics_sum_views')
                  ->orderBy('id')
                  ->paginate(perPage: 16)
        );
    }

    public function show(Author $author)
    {
        return new AuthorResource($author);
    }

    public function comics(Author $author)
    {
        return ComicResource::collection(
            $author->comics()
                   ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                   ->withCount('pages')
                   ->orderByDesc('views')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }

    public function characters(Author $author)
    {
        return CharacterResource::collection(
            $author->characters()
                   ->with('comics', fn(BelongsToMany $comics) => $comics
                       ->where('author_id', $author->id)
                       ->inRandomOrder()
                       ->take(1)
                   )
                   ->with('aliases')
                   ->withCount(['comics' => fn(Builder $comics) => $comics->where('author_id', $author->id)])
                   ->orderByDesc('comics_count')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }

    public function tags(Author $author)
    {
        return TagResource::collection(
            $author->tags()
                   ->with('comics', fn(BelongsToMany $comics) => $comics
                       ->where('author_id', $author->id)
                       ->inRandomOrder()
                       ->take(1)
                   )
                   ->withCount(['comics' => fn(Builder $comics) => $comics->where('author_id', $author->id)])
                   ->orderByDesc('comics_count')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }

    public function titles(Author $author)
    {
        return TitleResource::collection(
            $author->titles()
                   ->with('comics', fn(BelongsToMany $comics) => $comics
                       ->where('author_id', $author->id)
                       ->inRandomOrder()
                       ->take(1)
                   )
                   ->withCount(['comics' => fn(Builder $comics) => $comics->where('author_id', $author->id)])
                   ->orderByDesc('comics_count')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }

    public function characterComics(Author $author, Character $character)
    {
        return ComicResource::collection(
            $author->comics()
                   ->whereHas('characters', fn(Builder $characters) => $characters->where('characters.id', $character->id))
                   ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                   ->withCount('pages')
                   ->orderByDesc('views')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }

    public function tagComics(Author $author, Tag $tag)
    {
        return ComicResource::collection(
            $author->comics()
                   ->whereHas('tags', fn(Builder $tags) => $tags->where('tags.id', $tag->id))
                   ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                   ->withCount('pages')
                   ->orderByDesc('views')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }

    public function titleComics(Author $author, Title $title)
    {
        return ComicResource::collection(
            $author->comics()
                   ->whereHas('titles', fn(Builder $titles) => $titles->where('titles.id', $title->id))
                   ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                   ->withCount('pages')
                   ->orderByDesc('views')
                   ->orderBy('id')
                   ->paginate(perPage: 16)
        );
    }
}
