<?php

namespace Tmd\LaravelHelpers\Repositories\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Laravel\Scout\Builder as ScoutBuilder;
use Laravel\Scout\Engines\Engine as ScoutEngine;
use Tmd\LaravelHelpers\Libraries\Pagination\LastHiddenLengthAwarePaginator;
use Tmd\LaravelHelpers\Libraries\Pagination\LengthAwarePaginator;

trait ReturnsPaginatorsTrait
{
    protected function getPerPage()
    {
        return 10;
    }

    /**
     * @param QueryBuilder|EloquentBuilder|ScoutBuilder $builder
     * @param int $currentPage
     * @param int $perPage
     * @param bool $canJumpToLast
     * @param int|null $totalItems
     *
     * @return LengthAwarePaginator
     */
    protected function makePaginatorFromBuilder(
        $builder,
        $currentPage = 1,
        $perPage = null,
        $canJumpToLast = true,
        $totalItems = null
    ) {
        if ($builder instanceof EloquentBuilder) {
            $baseBuilder = $builder->toBase();
        } else {
            $baseBuilder = $builder;
        }

        if (!$currentPage) {
            $currentPage = 1;
        }

        if (is_null($perPage)) {
            $perPage = $this->getPerPage();
        }

        if (is_null($totalItems)) {
            $totalItems = $baseBuilder->getCountForPagination();
        }

        $items = $builder->forPage($currentPage, $perPage)->get();

        // Note: path is not set on the paginator so call setPath() on the returned paginator before use.
        if ($canJumpToLast) {
            return new LengthAwarePaginator($items, $totalItems, $perPage, $currentPage);
        } else {
            return new LastHiddenLengthAwarePaginator($items, $totalItems, $perPage, $currentPage);
        }
    }

    protected function makePaginatorFromScoutBuilder(
        EloquentModel $model,
        ScoutBuilder $builder,
        $currentPage = 1,
        $perPage = null,
        $canJumpToLast = true,
        $totalItems = null
    ) {
        $engine = $model->searchableUsing();

        if (!$currentPage) {
            $currentPage = 1;
        }

        if (is_null($perPage)) {
            $perPage = $this->getPerPage();
        }

        $items = Collection::make(
            $engine->map(
                $rawResults = $engine->paginate($builder, $perPage, $currentPage),
                $model
            )
        );

        if (is_null($totalItems)) {
            $totalItems = $engine->getTotalCount($rawResults);
        }

        // Note: path is not set on the paginator so call setPath() on the returned paginator before use.
        if ($canJumpToLast) {
            return new LengthAwarePaginator($items, $totalItems, $perPage, $currentPage);
        } else {
            return new LastHiddenLengthAwarePaginator($items, $totalItems, $perPage, $currentPage);
        }
    }
}
