<?php

namespace Tmd\LaravelHelpers\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

trait AcceptsModelsOrIdsTrait
{
    protected function getKey($parameter)
    {
        if (is_int($parameter)) {
            return $parameter;
        } elseif ($parameter instanceof Model) {
            return $parameter->getKey();
        }

        throw new \Exception("The given property must be an integer or a Model.");
    }
}
