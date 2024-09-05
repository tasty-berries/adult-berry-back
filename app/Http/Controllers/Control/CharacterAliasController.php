<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Resources\Control\CharacterAliasResource;
use App\Http\Resources\Control\CharacterResource;
use App\Models\Character;
use App\Models\CharacterAlias;
use App\Models\Comic;
use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharacterAliasController extends Controller
{
    public function index(Character $character)
    {
        return CharacterAliasResource::collection(
            $character->aliases
        );
    }

    public function store(Request $request, Character $character)
    {
        $data = $request->validate([
            'character_id' => 'required|exists:characters,id'
        ]);

        DB::transaction(function () use ($character, $data) {
            $reference = Character::find($data['character_id']);

            $character->aliases()->create([
                'character_id' => $character->id,
                'name'         => $reference->name,
                'link'         => $reference->link
            ]);

            $character->comics()->syncWithoutDetaching($reference->comics->map(fn(Comic $comic) => $comic->id)->all());

            $reference->delete();
        });
    }

    public function destroy(Character $character, CharacterAlias $alias)
    {
        DB::transaction(function () use ($character, $alias) {
            $restored = Character::create([
                'name' => $alias->name,
                'link' => $alias->link
            ]);

            $restored->comics()->syncWithoutDetaching($alias->character->comics->map(fn(Comic $comic) => $comic->id)->all());

            $alias->delete();
        });
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
                     ->take(10)
                     ->get()
        );
    }
}
