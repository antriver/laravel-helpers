<?php

namespace Tmd\LaravelSite\Libraries\Debug\Events;

class LocalCacheHit
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
