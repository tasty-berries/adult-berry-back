<?php

namespace App\Http\Resources;

use App\Models\ComicPage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ComicPage */
class ComicPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'image' => $this->image->path
        ];
    }
}
