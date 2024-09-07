<?php

namespace App\Http\Resources;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Video */
class VideoPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'video'   => new VideoResource($this->resource),
            'popular' => VideoResource::collection(
                Video::query()
                     ->whereNot('id', $this->id)
                     ->whereNotNull('video_id')
                     ->inRandomOrder()
                     ->take(10)
                     ->get()
            )
        ];
    }
}
