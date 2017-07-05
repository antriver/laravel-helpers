<?php

namespace Tmd\LaravelSite\Libraries\Debug\Events;

class LocalKeyWritten
{
    public $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
}
