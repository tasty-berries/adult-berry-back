<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\ComicController;
use App\Http\Controllers\Control;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\Video;
use Illuminate\Support\Facades\Route;

Route::get('comics', [ComicController::class, 'index']);
Route::get('comics/{comic}', [ComicController::class, 'show']);

Route::get('characters', [CharacterController::class, 'index']);
Route::get('characters/{character}', [CharacterController::class, 'show']);
Route::get('characters/{character}/comics', [CharacterController::class, 'comics']);
Route::get('characters/{character}/tags', [CharacterController::class, 'tags']);
Route::get('characters/{character}/tags/{tag}', [CharacterController::class, 'tagComics']);
Route::get('characters/{character}/titles', [CharacterController::class, 'titles']);
Route::get('characters/{character}/authors', [CharacterController::class, 'authors']);

Route::get('tags', [TagController::class, 'index']);
Route::get('tags/{tag}', [TagController::class, 'show']);
Route::get('tags/{tag}/comics', [TagController::class, 'comics']);
Route::get('tags/{tag}/characters', [TagController::class, 'characters']);
Route::get('tags/{tag}/titles', [TagController::class, 'titles']);
Route::get('tags/{tag}/authors', [TagController::class, 'authors']);

Route::get('titles', [TitleController::class, 'index']);
Route::get('titles/{title}', [TitleController::class, 'show']);
Route::get('titles/{title}/comics', [TitleController::class, 'comics']);
Route::get('titles/{title}/characters', [TitleController::class, 'characters']);
Route::get('titles/{title}/characters/{character}', [TitleController::class, 'characterComics']);
Route::get('titles/{title}/tags', [TitleController::class, 'tags']);
Route::get('titles/{title}/tags/{tag}', [TitleController::class, 'tagComics']);
Route::get('titles/{title}/authors', [TitleController::class, 'authors']);

Route::get('authors', [AuthorController::class, 'index']);
Route::get('authors/{author}', [AuthorController::class, 'show']);
Route::get('authors/{author}/comics', [AuthorController::class, 'comics']);
Route::get('authors/{author}/characters', [AuthorController::class, 'characters']);
Route::get('authors/{author}/characters/{character}', [AuthorController::class, 'characterComics']);
Route::get('authors/{author}/tags', [AuthorController::class, 'tags']);
Route::get('authors/{author}/tags/{tag}', [AuthorController::class, 'tagComics']);
Route::get('authors/{author}/titles', [AuthorController::class, 'titles']);
Route::get('authors/{author}/titles/{title}', [AuthorController::class, 'titleComics']);


Route::prefix('video')->group(function () {
    Route::get('videos', [Video\VideoController::class, 'index']);
    Route::get('videos/{video}', [Video\VideoController::class, 'show']);

    Route::get('authors', [Video\AuthorController::class, 'index']);
    Route::get('authors/{author}', [Video\AuthorController::class, 'show']);
    Route::get('authors/{author}/videos', [Video\AuthorController::class, 'videos']);

    Route::get('tags', [Video\TagController::class, 'index']);
    Route::get('tags/{tag}', [Video\TagController::class, 'show']);
    Route::get('tags/{tag}/videos', [Video\TagController::class, 'videos']);

    Route::get('sections', [Video\SectionController::class, 'index']);
    Route::get('sections/{section}', [Video\SectionController::class, 'show']);
    Route::get('sections/{section}/videos', [Video\SectionController::class, 'videos']);

    Route::get('titles', [Video\TitleController::class, 'index']);
    Route::get('titles/{title}', [Video\TitleController::class, 'show']);
    Route::get('titles/{title}/videos', [Video\TitleController::class, 'videos']);
});

Route::get('search', [SearchController::class, 'index']);

Route::prefix('control')->group(function () {
    Route::post('login', [Control\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('titles', Control\TitleController::class);

        Route::get('characters/search', [Control\CharacterController::class, 'search']);
        Route::apiResource('characters', Control\CharacterController::class);

        Route::get('characters/{character}/aliases/search', [Control\CharacterAliasController::class, 'search']);
        Route::apiResource('characters.aliases', Control\CharacterAliasController::class);
    });
});
