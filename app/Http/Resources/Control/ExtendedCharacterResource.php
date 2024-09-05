<?php

namespace App\Http\Resources\Control;

use App\Models\Title;
use Illuminate\Http\Request;

/** @mixin Title */
class ExtendedCharacterResource extends CharacterResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'description' => $this->description ?? ''
        ];
    }
}
