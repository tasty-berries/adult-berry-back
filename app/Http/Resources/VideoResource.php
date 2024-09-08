<?php

namespace App\Http\Resources;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Video */
class VideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'preview'      => $this->preview?->path,
            'views'        => number_format($this->views),
            'video'        => $this->video?->path,
            'author'       => new AuthorResource($this->author),
            'hentai_title' => new TitleResource($this->hentaiTitle),
            'tags'    => TagResource::collection($this->whenLoaded('tags'))
        ];
    }
}
