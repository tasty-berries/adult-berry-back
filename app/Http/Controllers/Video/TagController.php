<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Http\Resources\VideoResource;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        return TagResource::collection(
            Tag::has('videos')
               ->withCount('videos')
               ->orderByDesc('videos_count')
               ->get()
        );
    }

    public function show(Tag $tag)
    {
        return new TagResource($tag);
    }

    public function videos(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        return VideoResource::collection(
            $tag->videos()
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
