<?php

namespace Tmd\LaravelSite\ModelPresenters\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Tmd\LaravelSite\Libraries\Pagination\LengthAwarePaginator;
use Tmd\LaravelSite\ModelPresenters\Base\ModelPresenterInterface;

trait PresentArrayTrait
{
    /**
     * @param Model[]|\Iterator|Collection $models
     * @param array $args
     *
     * @return array
     */
    public function presentArray($models, ...$args): array
    {
        /** @var ModelPresenterInterface $this */
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
