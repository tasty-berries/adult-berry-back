<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Character;
use App\Models\CharacterAlias;
use App\Models\Comic;
use App\Models\SearchIndex;
use App\Models\Tag;
use App\Models\Title;
use Illuminate\Console\Command;
use function Laravel\Prompts\progress;

class GenerateSearchIndicesCommand extends Command
{
    protected $signature = 'generate:search-indices';

    protected $description = 'Command description';

    public function handle(): void
    {
        SearchIndex::truncate();

        $grouped = collect([
            ...Comic::orderBy('views')->get(),
            ...Character::withCount('comics')->orderByDesc('comics_count')->get(),
            ...CharacterAlias::with('character')->get(),
            ...Tag::withCount('comics')->orderByDesc('comics_count')->get(),
            ...Title::withCount('comics')->orderByDesc('comics_count')->get(),
            ...Author::withSum('comics', 'views')->orderByDesc('comics_sum_views')->get()
        ])->groupBy(fn($item) => match ($item::class) {
            CharacterAlias::class => Character::class,
            default               => $item::class
        });

        $result = collect();

        $chunkSize = 5;

        while ($grouped->flatten(1)->isNotEmpty()) {
            $grouped->each(fn($group) => $result->push(...$group->splice(0, $chunkSize)));
        }

        progress(
            label: 'Generating items...',
            steps: $result,
            callback: fn($item) => SearchIndex::create([
                'searchable_type' => match ($item::class) {
                    CharacterAlias::class => Character::class,
                    default               => $item::class
                },
                'searchable_id'   => $item->id,
                'name'            => $item->title ?? $item->name,
                'extra'           => match ($item::class) {
                    Character::class      => ['title' => $item->definedTitles->map(fn($title) => $title->name)],
                    CharacterAlias::class => [
                        'title'    => $item->character->definedTitles->map(fn($title) => $title->name),
                        'original' => ['id' => $item->character->id, 'name' => $item->character->name]
                    ],
                    default               => []
                }
            ])
        );
    }
}
