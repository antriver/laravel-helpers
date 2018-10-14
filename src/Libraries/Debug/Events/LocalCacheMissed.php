<?php

namespace Tmd\LaravelHelpers\Libraries\Debug\Events;

class LocalCacheMissed
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
