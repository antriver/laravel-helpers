<?php

namespace Tmd\LaravelSite\Libraries\Traits;

use Config;
use Illuminate\Support\Str;

trait GeneratesTokensTrait
{
    /**
     * @return string
     */
    protected function getHashKey()
    {
        return Config::get('app.key');
    }

    /**
     * Create a new token string.
     *
     * @return string
     */
    protected function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), $this->getHashKey());
    }
}
