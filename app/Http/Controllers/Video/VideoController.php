<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoPageResource;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255',
            'ids'    => 'nullable|array|exists:videos,id',
        ]);

        return VideoResource::collection(
            Video::query()
                 ->when($data['search'] ?? false, fn(Builder $when) => $when
                     ->where('title', 'LIKE', '%' . $data['search'] . '%')
                 )
                 ->when($data['ids'] ?? false, fn(Builder $when) => $when
                     ->whereIn('id', $data['ids'])
                 )
                 ->whereNotNull('video_id')
                 ->orderByDesc('views')
                 ->orderBy('id')
                 ->paginate(perPage: 40)
        );
    }

    public function show(Video $video)
    {
        return new VideoPageResource($video->load('tags'));
    }
}
