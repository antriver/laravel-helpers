<?php

namespace Tmd\LaravelSite\ModelPresenters\Base;

use Illuminate\Database\Eloquent\Model;

interface ModelPresenterInterface
{
    /**
     * @param Model $model
     *
     * @return array
     */
    public function present(Model $model): array;

    /**
     * @param Model[] $models
     *
     * @return array[]
     */
    public function presentArray($models): array;
}
