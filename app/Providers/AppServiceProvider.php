<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Str::macro('allowedContent', function (string $raw) {
            $content = explode(',', $raw);
            return [
                'vanilla' => in_array('vanilla', $content),
                'gay'     => in_array('gay', $content),
                'furry'   => in_array('furry', $content),
                'lolycon' => in_array('lolycon', $content),
                'lesbian' => in_array('lesbian', $content),
                'incest'  => in_array('incest', $content),
                'zoo'     => in_array('zoo', $content)
            ];
        });
    }
}
