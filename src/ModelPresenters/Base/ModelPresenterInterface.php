<?php

namespace Tmd\LaravelSite\ModelPresenters\Base;

use Illuminate\Database\Eloquent\Model;

interface ModelPresenterInterface
{
    /**
     * @param Model $model
     *
     * @return mixed
     */
    public function present(Model $model): array;

    /**
     * @param Model[] $models
     *
     * @return mixed
     */
    public function presentArray($models): array;
}
