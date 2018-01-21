<?php

namespace Tmd\LaravelSite\Libraries\Traits;

use Illuminate\Support\Str;

trait GeneratesTokensTrait
{
    /**
     * Create a new token string.
     *
     * @return string
     */
    protected function generateToken(): string
    {
        return hash_hmac('sha256', Str::random(40), $this->getHashKey());
    }

    /**
     * @return string
     */
    private function getHashKey()
    {
        return config('app.key');
    }
}
