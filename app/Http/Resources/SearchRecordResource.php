<?php

namespace App\Http\Resources;

use App\Enums\SearchRecordType;
use App\Models\SearchIndex;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin SearchIndex */
class SearchRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'type'        => SearchRecordType::fromClass($this->searchable_type),
            'resource_id' => $this->searchable_id,
            'extra'       => $this->extra
        ];
    }
}
