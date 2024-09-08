<?php

namespace App\Http\Resources\Control;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilesystemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'source'     => $this['src'],
            'filesystem' => $this['fs'],
            'size'       => (int)$this['size'],
            'used'       => (int)$this['used']
        ];
    }
}
