<?php

namespace App\Http\Resources\Control;

use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Character */
class CharacterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ? Str::limit(strip_tags($this->description), 50) : '',
            'preview'     => $this->preview?->path,
            ...isset($this['pivot']) ? ['role' => $this->pivot->role] : []
        ];
    }
}
