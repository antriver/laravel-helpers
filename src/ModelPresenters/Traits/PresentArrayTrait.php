<?php

namespace Tmd\LaravelSite\ModelPresenters\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Tmd\LaravelSite\Libraries\Pagination\LengthAwarePaginator;

trait PresentArrayTrait
{
    /**
     * @param Model[]|\Iterator $models
     * @param array $args
     *
     * @return array
     */
    public function presentArray($models, ...$args): array
    {
        $array = [];
        foreach ($models as $model) {
            $array[] = $this->present($model, ...$args);
        }

        return $array;
    }

    /**
     * @param \Illuminate\Contracts\Pagination\Paginator|Paginator|LengthAwarePaginator $paginator
     * @param array $args
     *
     * @return array
     */
    public function presentPaginator(\Illuminate\Contracts\Pagination\Paginator $paginator, ...$args)
    {
        $paginator->getCollection()->transform(
            function ($item) use ($args) {
                return $this->present($item, ...$args);
            }
        );
    }
}
