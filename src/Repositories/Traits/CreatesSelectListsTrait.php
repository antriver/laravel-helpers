<?php

namespace Tmd\LaravelHelpers\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Helps with building an HTML <select> list
 */
trait CreatesSelectListsTrait
{
    /**
     * @param array   $list
     * @param Model[] $items
     * @param         $nameField
     * @param         $keyField
     * @param         $nextDepthFunction
     * @param int     $depth
     */
    protected function appendToSelectList(array &$list, $items, $nameField, $keyField, $nextDepthFunction, $depth = 0)
    {
        foreach ($items as $item) {
            $list[$item->getAttribute($keyField)] = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $depth)
                .$item->getAttribute($nameField).' ['.$item->getAttribute($keyField).']';

            $this->appendToSelectList(
                $list,
                $nextDepthFunction($item),
                $nameField,
                $keyField,
                $nextDepthFunction,
                $depth + 1
            );
        }
    }
}
