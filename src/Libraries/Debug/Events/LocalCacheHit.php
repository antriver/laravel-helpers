<?php

namespace Tmd\LaravelHelpers\Libraries\Debug\Events;

class LocalCacheHit
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
