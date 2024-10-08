<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\File;
use App\Models\Section;
use App\Models\Tag;
use App\Models\Video;
use App\Services\Html\Next\Element;
use App\Services\Html\Next\Filter;
use App\Services\Html\Next\Parser;
use App\Services\Http\ProxyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Prompts\Progress;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

class ParseVideoContentCommand extends Command
{
    protected $signature = 'parse:video-content {page?}';

    protected $description = 'Command description';

    private function makeFile(ProxyService $proxy, Element $element, string $prefix = 'previews'): ?File
    {
        $link       = $element->attributes['src'];
        $filename   = basename(parse_url($link)['path']);
        $extParts   = explode('.', $filename);
        $ext        = $extParts[count($extParts) - 1];
        $myFilename = Str::uuid() . '.' . $ext;

        $file = File::where('link', $link)->first();

        if (!$file) {
            $content = $proxy->through(timeout: 60)->get($link);

            if (!$content->ok())
                return null;

            $file = File::create([
                'link' => $link,
                'path' => Storage::disk('public')->put("$prefix/" . $myFilename, $content->body()) ? "$prefix/" . $myFilename : null
            ]);
        }

        return $file;
    }

    public function handle(ProxyService $proxy, Parser $parser): void
    {
        $page = (int)$this->argument('page');

        $response = spin(
            callback: fn() => $proxy->through()->get('https://multporn.net/random', [
                'sort_by'    => 'title',
                'sort_order' => 'ASC',
                'type'       => 7,
                'page'       => $page
            ])->body(),
            message: 'Parsing page...'
        );

        $document = $parser->parse($response);

        $tds = $document->findAll(new Filter(name: 'td'));

        $this->info('Page parsed. Found ' . count($tds) . ' elements.');

        /** @var Video[] $videos */
        $videos = [];

        progress(
            label: 'Parsing elements',
            steps: $tds,
            callback: function (Element $td, Progress $progress) use (&$videos, $proxy) {
                $titleEl = $td
                    ->find(new Filter(class: "views-field views-field-title"))
                    ?->find(new Filter(name: "a")) ?? null;

                if (!$titleEl)
                    return;

                $data = [
                    "title" => html_entity_decode($titleEl->text),
                    "link"  => $titleEl->attributes["href"],
                    "views" => preg_match(
                        '/^Views: ([0-9,]+)$/',
                        $td
                            ->find(new Filter(class: "views-field views-field-totalcount"))
                            ->find(new Filter(name: "h5"))->text,
                        $matches
                    ) ? (int)Str::replace(",", "", $matches[1]) : null
                ];

                $previewEl = $td
                    ->find(new Filter(class: "views-field views-field-field-vd-preciew"))
                    ->find(new Filter(name: "img"));

                $video = Video::updateOrCreate(
                    ['link' => $data['link']],
                    [
                        'title'      => $data['title'],
                        'views'      => $data['views'],
                        'preview_id' => $previewEl ? $this->makeFile($proxy, $previewEl)?->id : null
                    ]
                );

                $videos[] = $video;

                $progress->hint($data['title']);
            }
        );

        foreach ($videos as $video) {
            $this->info('[' . $video->title . ']');

            $response = spin(
                callback: fn() => $proxy->through()->get('https://multporn.net' . $video->link)->body(),
                message: '~ Parsing video...'
            );

            $document = $parser->parse($response);

            $videoEl = $document->find(new Filter(name: 'video'))?->find(new Filter(name: 'source'));

            if (!$videoEl) {
                $this->warn('~ Video not parsed: video tag not found.');
                continue;
            }

            $this->info('~ Video parsed!');

            $videoFile = spin(
                callback: fn() => $this->makeFile($proxy, $videoEl, 'videos'),
                message: '~ Downloading video...'
            );

            if ($videoFile && $videoFile->wasRecentlyCreated) {
                $video->video_id = $videoFile->id;
                $this->info('~ Video downloaded!');
            } else if ($videoFile) {
                $this->warn('~ Video already exist.');
            } else {
                $this->error('~ Video not found.');
            }

            $authorEl = $document->find(new Filter(class: 'field field-name-field-vd-authors field-type-taxonomy-term-reference field-label-above clearfix'))
                                 ?->find(new Filter(name: 'a'));

            if ($authorEl) {
                $video->author_id = Author::firstOrCreate(
                    ['link' => $authorEl->attributes['href']],
                    ['name' => html_entity_decode($authorEl->text)]
                )->id;

                $this->info('~ Author created/updated.');
            }

            $tagsEl = $document->find(new Filter(class: 'field field-name-field-vd-tags field-type-taxonomy-term-reference field-label-above clearfix'))
                               ?->findAll(new Filter(name: 'a')) ?? [];

            if ($tagsEl) {
                $video->tags()->sync(
                    collect($tagsEl)
                        ->map(fn(Element $element) => Tag::firstOrCreate(
                            ['link' => $element->attributes['href']],
                            ['name' => html_entity_decode($element->text)]
                        ))
                        ->map(fn(Tag $tag) => $tag->id)
                );

                $this->info('~ Tags synced.');
            }

            $sectionEl = $document->find(new Filter(class: 'field field-name-field-vd-group field-type-taxonomy-term-reference field-label-above clearfix'))
                                  ?->find(new Filter(name: 'a'));

            if ($sectionEl) {
                $video->section_id = Section::firstOrCreate(
                    ['link' => $sectionEl->attributes['href']],
                    ['name' => html_entity_decode($sectionEl->text)]
                )->id;

                $this->info('~ Section created/updated.');
            }

            $video->save();

            $this->info("");
        }
    }
}
