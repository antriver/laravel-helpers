<?php

namespace Tmd\LaravelSite\ModelPresenters\Base;

use Tmd\LaravelSite\Models\Base\AbstractModel;

interface ModelPresenterInterface
{
    /**
     * @param AbstractModel $model
     *
     * @return mixed
     */
    public function present(AbstractModel $model): array;

    /**
     * @param array $models
     *
     * @return mixed
     */
    public function presentArray($models): array;
}
