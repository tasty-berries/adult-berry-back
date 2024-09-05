<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Resources\Control\CharacterResource;
use App\Http\Resources\Control\ExtendedCharacterResource;
use App\Models\Character;
use App\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CharacterController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        return CharacterResource::collection(
            Character::query()
                     ->when($data['search'] ?? false, fn(Builder $when) => $when
                         ->where('name', 'LIKE', "%{$data['search']}%")
                     )
                     ->withCount('comics')
                     ->orderByDesc('comics_count')
                     ->paginate(perPage: 50)
        );
    }

    public function show(Character $character)
    {
        return new ExtendedCharacterResource($character);
    }

    public function update(Request $request, Character $character)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:65536',
            'preview'     => 'nullable|image|max:2048'
        ]);

        $character->update([
            ...$data,
            ...isset($data['preview']) ? [
                'preview_id' => File::create([
                    'path' => $request->file('preview')?->storeAs(
                        'previews',
                        Str::uuid() . '.' . $request->file('preview')->clientExtension(),
                        'public'
                    )
                ])->id
            ] : []
        ]);
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'query' => 'nullable|string|max:255'
        ]);

        return CharacterResource::collection(
            Character::query()
                     ->when($data['query'] ?? false, fn(Builder $when) => $when
                         ->where('name', 'LIKE', "%{$data['query']}%")
                     )
                     ->withCount('comics')
                     ->orderByDesc('comics_count')
                     ->take(10)
                     ->get()
        );
    }
}
