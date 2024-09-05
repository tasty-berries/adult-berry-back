<?php

namespace App\Http\Controllers\Control;

use App\Http\Controllers\Controller;
use App\Http\Resources\Control\ExtendedTitleResource;
use App\Http\Resources\Control\TitleResource;
use App\Models\File;
use App\Models\Title;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TitleController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        return TitleResource::collection(
            Title::query()
                 ->when($data['search'] ?? false, fn(Builder $when) => $when
                     ->where('name', 'LIKE', "%{$data['search']}%")
                 )
                 ->withCount('comics')
                 ->orderByDesc('comics_count')
                 ->orderBy('id')
                 ->paginate(perPage: 50)
        );
    }

    public function show(Title $title)
    {
        return new ExtendedTitleResource($title);
    }

    public function update(Request $request, Title $title)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:65536',
            'characters'  => 'nullable|array',
            'preview'     => 'nullable|image|max:4096'
        ]);

        $title->update([
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

        if (isset($data['characters'])) {
            $title->definedCharacters()->sync(
                collect($data['characters'])->map(fn($role) => ['role' => $role])->all()
                ?? []
            );
        }
    }
}
