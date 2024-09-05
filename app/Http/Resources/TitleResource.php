<?php

namespace App\Http\Resources;

use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Title */
class TitleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'link'        => $this->link,
            'description' => $this->description,
            'preview'     => $this->preview?->path,
            'comics'      => ComicResource::collection($this->whenLoaded('comics')),
            ...isset($this['comics_count']) ? ['comics_count' => $this->comics_count] : []
        ];
    }
}
