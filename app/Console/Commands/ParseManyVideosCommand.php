<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ParseManyVideosCommand extends Command
{
    protected $signature = 'parse:many-videos {from} {to}';

    protected $description = 'Command description';

    public function handle(): void
    {
        $from = $this->argument('from');
        $to   = $this->argument('to');

        for ($i = $from; $i <= $to; $i++) {
            $this->info("Page $i...");
            Artisan::call('parse:video-content', ['page' => $i]);
        }
    }
}
