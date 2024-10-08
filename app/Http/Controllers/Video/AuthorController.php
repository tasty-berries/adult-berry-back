<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\VideoResource;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'ids' => 'nullable|array|exists:authors,id'
        ]);

        return AuthorResource::collection(
            Author::has('videos')
                  ->when($data['ids'] ?? false, fn(Builder $when) => $when
                      ->whereIn('id', $data['ids'])
                  )
                  ->withCount('videos')
                  ->orderByDesc('videos_count')
                  ->get()
        );
    }

    public function show(Author $author)
    {
        return new AuthorResource($author);
    }

    public function videos(Request $request, Author $author)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        return VideoResource::collection(
            $author->videos()
                   ->when($data['search'] ?? false, fn(Builder $when) => $when
                       ->where('title', 'LIKE', '%' . $data['search'] . '%')
                   )
                   ->whereNotNull('videos.video_id')
                   ->orderByDesc('views')
                   ->orderBy('videos.id')
                   ->paginate(perPage: 40)
        );
    }
}
