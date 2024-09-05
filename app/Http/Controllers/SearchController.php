<?php

namespace App\Http\Controllers;

use App\Http\Resources\SearchRecordResource;
use App\Models\SearchIndex;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const CHUNK_SIZE = 5;

    public function index(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255'
        ]);

        return SearchRecordResource::collection(
            SearchIndex::query()
                       ->fromSub(
                           SearchIndex::selectRaw(
                               "*, ROW_NUMBER() OVER (PARTITION BY searchable_type ORDER BY id) AS n"
                           )->when($data['name'] ?? false, fn(Builder $when) => $when
                               ->where('name', 'LIKE', "%{$data['name']}%")
                           ),
                           "x"
                       )
                       ->where("n", "<=", self::CHUNK_SIZE)
                       ->get()
        );
    }
}
