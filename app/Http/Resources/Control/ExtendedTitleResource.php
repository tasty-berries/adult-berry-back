<?php

namespace App\Http\Resources\Control;

use App\Models\Title;
use Illuminate\Http\Request;

/** @mixin Title */
class ExtendedTitleResource extends TitleResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'description' => $this->description ?? '',
            'characters'  => CharacterResource::collection(
                $this->definedCharacters()->withPivot('role')->get()
            )
        ];
    }
}
