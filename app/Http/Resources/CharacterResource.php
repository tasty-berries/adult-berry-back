<?php

namespace App\Http\Resources;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Character */
class CharacterResource extends JsonResource
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
            'role'        => count($this->definedTitles) > 0 ? $this->definedTitles->first()->pivot->role : null,
            'aliases'     => CharacterAliasResource::collection($this->whenLoaded('aliases')),
            'titles'      => TitleResource::collection($this->whenLoaded('definedTitles')),
            ...isset($this['comics_count']) ? ['comics_count' => $this->comics_count] : []
        ];
    }
}
