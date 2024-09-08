<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Resources\Control\FilesystemResource;

class FilesystemController extends Controller
{
    public function index()
    {
        $matches = [];

        if (!preg_match_all(
            "/^(?<src>[a-z0-9\/]+)\s*(?<fs>ext4|tmpfs)\s*(?<size>[0-9]+)\s*(?<used>[0-9]+)$/im",
            `df --output=source,fstype,size,used`,
            $matches
        )) abort(404);

        $items = [];

        for ($i = 0; $i < count($matches[0]); $i++) {
            $items[] = [
                'src'  => $matches['src'][$i],
                'fs'   => $matches['fs'][$i],
                'size' => $matches['size'][$i],
                'used' => $matches['used'][$i]
            ];
        }

        return FilesystemResource::collection($items);
    }
}

