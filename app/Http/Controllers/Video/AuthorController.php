<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\VideoResource;
use App\Models\Author;

class AuthorController extends Controller
{
    public function show(Author $author)
    {
        return new AuthorResource($author);
    }

    public function videos(Author $author)
    {
        return VideoResource::collection(
            $author->videos()
                   ->orderByDesc('views')
                   ->get()
        );
    }
}
