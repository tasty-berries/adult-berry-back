<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\TitleResource;
use App\Http\Resources\VideoResource;
use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TitleController extends Controller
{
    public function index()
    {
        return TitleResource::collection(
            Title::has('videos')
                 ->orderBy('name')
                 ->get()
        );
    }

    public function show(Title $title)
    {
        return new TitleResource($title);
    }

    public function videos(Request $request, Title $title)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        return VideoResource::collection(
            $title->videos()
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
