<?php

namespace App\Services\Http;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class ProxyService
{
    public function through($includeUserAgent = true): PendingRequest
    {
        return Http::withOptions([
            'proxy'           => config('services.proxy'),
            'headers'         => [
                'User-Agent' => $includeUserAgent ? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36' : '',
            ],
            'timeout'         => 5,
            'connect_timeout' => 5
        ]);
    }
}
