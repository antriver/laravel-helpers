<?php

namespace Tmd\LaravelSite\Libraries\Debug\Events;

class LocalCacheMissed
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
