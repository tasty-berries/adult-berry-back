<?php

use App\Http\Controllers\ComicController;
use App\Services\Html\HtmlElement;
use App\Services\Html\HtmlParser;
use App\Services\Html\Next\Element;
use App\Services\Html\Next\Filter;
use App\Services\Html\Next\Parser;
use App\Services\Http\ProxyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', [ComicController::class, 'index']);

/**
 * Route::get('/', function (ProxyService $proxy, HtmlParser $parser) {
 * $body = Cache::rememberForever('comics', function () use ($proxy) {
 * $response = $proxy->through()->get('https://multporn.net/sort_comics');
 * return $response->body();
 * });
 *
 * $document = $parser->parse($body);
 *
 * $comics = [];
 *
 * foreach ($document->findAllRecursive(['tag' => 'td']) as $element) {
 * $previewLink = $element->find(['class' => 'views-field views-field-field-preview'])
 * ->find(['class' => 'field-content'])
 * ->find(['tag' => 'a']);
 *
 * $views = $element->find(['class' => 'views-field views-field-totalcount'])
 * ->find(['class' => 'field-content'])
 * ->firstText();
 *
 * $comics[] = [
 * 'title'   => Str::replace("\n", '', $element->find(['class' => 'views-field views-field-title'])->deepText()),
 * 'preview' => $previewLink->find(['tag' => 'img'])->attributes['src'],
 * 'link'    => $previewLink->attributes['href'],
 * 'views'   => (int)Str::replace(',', '', $views),
 * 'tags'    => collect(
 * $element->find(['class' => 'views-field views-field-field-category'])
 * ->findAll(['tag' => 'a'])
 * )->map(fn(HtmlElement $tag) => $tag->firstText())
 * ];
 * }
 *
 * return response()->json($comics);
 * });
 */

Route::get('/comic/{image?}', function (ProxyService $proxy, Parser $parser, int $image = 0) {
    $body = Cache::rememberForever('comic', function () use ($proxy) {
        $response = $proxy->through()->get('https://multporn.net/comics/sultry_summer');
        return $response->body();
    });

    $document = $parser->parse($body);
    $images   = collect($document->find(new Filter(name: 'noscript'))->findAll(new Filter(name: 'img')))
        ->map(fn(Element $element) => $element->attributes['src']);

    return view('comic', [
        'index'  => $image,
        'images' => $images
    ]);
});

Route::get('/proxy', function (Request $request, ProxyService $proxy) {
    $data = $request->validate([
        'url' => 'required|url'
    ]);

    $body = Cache::rememberForever($data['url'], function () use ($proxy, $data) {
        return $proxy->through()->get($data['url'])->body();
    });

    return response($body, 200)->header('Content-Type', 'image/png');
});

Route::get('proxy/test', function (Request $request, ProxyService $proxy) {
    return response()->json(
        $proxy->through()
              ->withHeaders(['Accept' => 'application/json'])
              ->get('http://ipinfo.io')
              ->json()
    );
});

Route::get('proxy/2ip', function (Request $request, ProxyService $proxy) {
    return response(
        $proxy->through()
              ->get('https://2ip.ru')
              ->body()
    );
});

Route::get('proxy/mp', function (Request $request, ProxyService $proxy) {
    return response(
        $proxy->through()
              ->get('https://multporn.net')
              ->body()
    );
});
