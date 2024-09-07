<?php

namespace App\Http\Resources;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Author */
class AuthorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'link'   => $this->link,
            'comics' => ComicResource::collection($this->whenLoaded('comics')),
            'videos' => VideoResource::collection($this->whenLoaded('videos')),
            ...isset($this['comics_count']) ? ['comics_count' => $this->comics_count] : [],
            ...isset($this['comics_sum_views']) ? ['comics_sum_views' => number_format($this->comics_sum_views)] : []
        ];
    }
}
