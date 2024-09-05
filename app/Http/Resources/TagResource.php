<?php

namespace App\Http\Resources;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Tag */
class TagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'name'   => $this->name,
            'link'   => $this->link,
            'comics' => ComicResource::collection($this->whenLoaded('comics')),
            ...isset($this['comics_count']) ? ['comics_count' => $this->comics_count] : []
        ];
    }
}
