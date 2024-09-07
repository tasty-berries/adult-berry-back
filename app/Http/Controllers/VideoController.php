<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoPageResource;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class VideoController extends Controller
{
    public function index()
    {
        return VideoResource::collection(
            Video::query()
                 ->whereNotNull('video_id')
                 ->orderByDesc('views')
                 ->get()
        );
    }

    public function show(Video $video)
    {
        return new VideoPageResource($video->load('tags'));
    }
}
