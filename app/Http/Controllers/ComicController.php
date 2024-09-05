<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComicResource;
use App\Models\Comic;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ComicController extends Controller
{
    public function index()
    {
        return ComicResource::collection(
            Comic::query()
                 ->with('tags', fn(BelongsToMany $tags) => $tags->orderBy('name')->take(5))
                 ->withCount('pages')
                 ->orderByDesc('views')
                 ->orderBy('id')
                 ->paginate(perPage: 16)
        );
    }

    public function show(Comic $comic)
    {
        return new ComicResource(
            $comic->load('pages')
                  ->load(['tags' => fn(BelongsToMany $tags) => $tags->orderBy('name')])
                  ->load(['characters' => fn(BelongsToMany $characters) => $characters->orderBy('name')])
                  ->load(['titles' => fn(BelongsToMany $titles) => $titles->orderBy('name')])
                  ->load('author')
        );
    }
}
