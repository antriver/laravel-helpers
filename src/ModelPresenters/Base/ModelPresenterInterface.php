<?php

namespace Tmd\LaravelSite\ModelPresenters\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ModelPresenterInterface
{
    /**
     * @param Model $model
     *
     * @return array
     */
    public function present(Model $model): array;

    /**
     * @param Model[]|\Iterator|Collection $models
     *
     * @return array[]
     */
    public function presentArray($models): array;
}
