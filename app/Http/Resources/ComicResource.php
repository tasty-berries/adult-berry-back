<?php

namespace App\Http\Resources;

use App\Models\Comic;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Comic */
class ComicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'preview'    => $this->preview?->path ?? $this->pages()->first()?->image?->path,
            'link'       => $this->link,
            'views'      => number_format($this->views),
            'tags'       => TagResource::collection($this->whenLoaded('tags')),
            'characters' => CharacterResource::collection($this->whenLoaded('characters')),
            'pages'      => ComicPageResource::collection($this->whenLoaded('pages')),
            'titles'     => TitleResource::collection($this->whenLoaded('titles')),
            'author'     => new AuthorResource($this->whenLoaded('author')),
            ...isset($this['pages_count']) ? ['pages_count' => $this->pages_count] : []
        ];
    }
}
