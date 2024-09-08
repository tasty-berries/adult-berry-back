<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Character;
use App\Models\CharacterAlias;
use App\Models\Comic;
use App\Models\ComicPage;
use App\Models\File;
use App\Models\Tag;
use App\Models\Title;
use App\Services\Html\Next\Element;
use App\Services\Html\Next\Filter;
use App\Services\Html\Next\Parser;
use App\Services\Http\ProxyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class ParseContentCommand extends Command
{
    protected $signature = 'parse:content {page?} {--fast}';

    protected $description = 'Command description';

    private function makePreview(ProxyService $proxy, Element $element): ?File
    {
        $link       = $element->attributes['src'];
        $filename   = basename(parse_url($link)['path']);
        $extParts   = explode('.', $filename);
        $ext        = $extParts[count($extParts) - 1];
        $myFilename = Str::uuid() . '.' . $ext;

        $file = File::where('link', $link)->first();

        if (!$file) {
            $content = $proxy->through()->get($link);

            if (!$content->ok())
                return null;

            $file = File::create([
                'link' => $link,
                'path' => Storage::disk('public')->put("previews/" . $myFilename, $content->body()) ? "previews/" . $myFilename : null
            ]);
        }

        return $file;
    }

    public function handle(ProxyService $proxy, Parser $parser): void
    {
        $page = (int)$this->argument('page');

        $response = $proxy->through()->get('https://multporn.net/sort_comics?page=' . $page)->body();

        $document = $parser->parse($response);

        $comicTds = $document->findAll(new Filter(name: 'td'));

        /** @var Comic[] $comics */
        $comics = [];

        progress(
            label: 'Parsing comics...',
            steps: $comicTds,
            callback: function (Element $td) use ($proxy, &$comics) {
                $titleAnchor = $td->find(new Filter(class: 'views-field views-field-title'))->find(new Filter(name: 'a'));

                $comic = Comic::withoutGlobalScopes()->where('link', $titleAnchor->attributes['href'])->first();

                if ($this->option('fast') && $comic) {
                    $comics[] = $comic->load('pages');
                    return;
                }

                $previewFile = $this->makePreview(
                    $proxy,
                    $td->find(new Filter(class: 'views-field views-field-field-preview'))->find(new Filter(name: 'img'))
                );

                if (!$comic) {
                    $comic = Comic::create([
                        'link'       => $titleAnchor->attributes['href'],
                        'title'      => html_entity_decode($titleAnchor->text),
                        'views'      => (int)Str::replace(',', '', $td->find(new Filter(class: 'views-field views-field-totalcount'))->find(new Filter(name: 'strong'))->text),
                        'preview_id' => $previewFile?->id
                    ]);
                } else {
                    $comic->update([
                        'title'      => html_entity_decode($titleAnchor->text),
                        'views'      => (int)Str::replace(',', '', $td->find(new Filter(class: 'views-field views-field-totalcount'))->find(new Filter(name: 'strong'))->text),
                        'preview_id' => $previewFile?->id
                    ]);
                }

                $comic->tags()->sync(
                    collect(
                        $td->find(new Filter(class: 'views-field views-field-field-category'))
                           ?->findAll(new Filter(name: 'a')) ?? []
                    )
                        ->map(fn(Element $element) => Tag::withoutGlobalScopes()->firstOrCreate(
                            ['link' => $element->attributes['href']],
                            ['name' => html_entity_decode($element->text)]
                        ))
                        ->map(fn(Tag $tag) => $tag->id)
                        ->all()
                );

                $comics[] = $comic->load('pages');
            }
        );

        foreach ($comics as $comic) {
            $comicResponse = spin(
                callback: fn() => $proxy->through()->get('https://multporn.net' . $comic->link)->body(),
                message: 'Fetching comic "' . $comic->title . '"...'
            );

            $comicDocument = $parser->parse($comicResponse);

            $images = $comicDocument->find(new Filter(name: 'noscript'))->findAll(new Filter(name: 'img'));

            if ($this->option('fast') && count($images) <= count($comic->pages))
                continue;

            $comic->tags()->sync(
                collect(
                    $comicDocument->find(new Filter(class: 'field field-name-field-category field-type-taxonomy-term-reference field-label-inline clearfix'))
                                  ?->findAll(new Filter(name: 'a')) ?? []
                )->map(fn(Element $element) => Tag::withoutGlobalScopes()->firstOrCreate(
                    ['link' => $element->attributes['href']],
                    ['name' => html_entity_decode($element->text)]
                ))
                 ->map(fn(Tag $tag) => $tag->id)
                 ->all()
            );

            $comic->characters()->sync(
                collect(
                    $comicDocument->find(new Filter(class: 'field field-name-field-characters field-type-taxonomy-term-reference field-label-inline clearfix'))
                                  ?->findAll(new Filter(name: 'a')) ?? []
                )->map(function (Element $element) {
                    $character = Character::withoutGlobalScopes()->where('link', $element->attributes['href'])->first();

                    if (!$character)
                        $character = CharacterAlias::withoutGlobalScopes()->where('link', $element->attributes['href'])->first()?->character;

                    if (!$character) {
                        $character = Character::withoutGlobalScopes()->create([
                            'name' => html_entity_decode($element->text),
                            'link' => $element->attributes['href']
                        ]);
                    }

                    return $character;
                })->map(fn(Character $character) => $character->id)
                 ->unique()
                 ->values()
            );

            $comic->titles()->sync(
                collect(
                    $comicDocument->find(new Filter(class: 'field field-name-field-com-group field-type-taxonomy-term-reference field-label-inline clearfix'))
                                  ?->findAll(new Filter(name: 'a')) ?? []
                )->reject(fn(Element $element) => in_array(html_entity_decode($element->text), [
                    'Ongoings',
                    'Others',
                    "Won't be finished",
                    'DC Universe',
                    'Crossovers',
                    'Marvel',
                    'YouTube',
                    'Furry',
                    'Halloween'
                ]))
                 ->map(fn(Element $element) => Title::withoutGlobalScopes()->firstOrCreate(
                     ['link' => $element->attributes['href']],
                     ['name' => html_entity_decode($element->text)]
                 ))
                 ->map(fn(Title $title) => $title->id)
                 ->all()
            );

            $author = $comicDocument->find(new Filter(class: 'field field-name-field-author field-type-taxonomy-term-reference field-label-inline clearfix'))
                                    ?->find(new Filter(name: 'a'));

            if ($author) {
                $comic->author_id = Author::withoutGlobalScopes()->firstOrCreate(
                    ['link' => $author->attributes['href']],
                    ['name' => html_entity_decode($author->text)]
                )->id;

                $comic->save();
            }

            progress(
                label: 'Parsing comic "' . $comic->title . '"...',
                steps: $images,
                callback: function (Element $img) use ($proxy, $comic) {
                    $imageFilename = Str::uuid();
                    $imageFile     = File::where('link', $img->attributes['src'])->first();

                    $filenameFromLink = basename(parse_url($img->attributes['src'])["path"]);
                    $dotted           = explode(".", $filenameFromLink);
                    $extension        = $dotted[count($dotted) - 1];

                    $imageFilename .= '.' . $extension;

                    if (
                        !$imageFile &&
                        ($imageResponse = $proxy->through()->get($img->attributes['src'])) &&
                        $imageResponse->ok()
                    ) {
                        $stored = Storage::disk('public')
                                         ->put('images/' . $imageFilename, $imageResponse->body());

                        $imageFile = File::create([
                            'link' => $img->attributes['src'],
                            'path' => $stored ? 'images/' . $imageFilename : null
                        ]);
                    }

                    if (!$imageFile)
                        return;

                    ComicPage::withoutGlobalScopes()->firstOrCreate([
                        'comic_id' => $comic->id,
                        'image_id' => $imageFile->id
                    ]);
                }
            );
        }
    }
}
