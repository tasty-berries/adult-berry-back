<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Http\Resources\VideoResource;
use App\Models\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return SectionResource::collection(
            Section::has('videos')
                   ->orderBy('name')
                   ->get()
        );
    }

    public function show(Section $section)
    {
        return new SectionResource($section);
    }

    public function videos(Request $request, Section $section)
    {
        $data = $request->validate([
            'search' => 'nullable|string|max:255'
        ]);

        return VideoResource::collection(
            $section->videos()
                    ->when($data['search'] ?? false, fn(Builder $when) => $when
                        ->where('title', 'LIKE', '%' . $data['search'] . '%')
                    )
                    ->whereNotNull('videos.video_id')
                    ->orderByDesc('views')
                    ->orderBy('videos.id')
                    ->paginate(perPage: 40)
        );
    }
}
