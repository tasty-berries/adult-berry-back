<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Str;

class LimitContentScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!request())
            return;

        $allowedContent = Str::allowedContent(request()->header('Allowed-Content', 'vanilla'));

        $notVanillaTags = [
            'Gay',
            'Furry',
            'Lolycon',
            'Lesbians',
            'Animals',
            'Very Close Relatives'
        ];

        $builder->when(!$allowedContent['vanilla'], fn(Builder $when) => $when->whereHas('tags', fn(Builder $has) => $has->whereIn('name', $notVanillaTags)))
                ->when(!$allowedContent['gay'], fn(Builder $when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has->where('name', 'Gay')))
                ->when(!$allowedContent['furry'], fn(Builder $when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has->where('name', 'Furry')))
                ->when(!$allowedContent['lolycon'], fn(Builder $when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has->where('name', 'Lolycon')))
                ->when(!$allowedContent['lesbian'], fn(Builder $when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has->where('name', 'Lesbians')))
                ->when(!$allowedContent['incest'], fn(Builder $when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has->where('name', 'Very Close Relatives')))
                ->when(!$allowedContent['zoo'], fn(Builder $when) => $when->whereDoesntHave('tags', fn(Builder $has) => $has->where('name', 'Animals')));
    }
}
