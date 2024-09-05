<?php

namespace App\Http\Resources\Control;

use App\Models\Title;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Title */
class TitleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description ? strip_tags($this->description) : '',
            'preview'     => $this->preview?->path
        ];
    }
}
