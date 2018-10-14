<?php

namespace Tmd\LaravelHelpers\Libraries\Traits;

trait ConvertsCaseTrait
{
    /**
     * @param array|object $data
     *
     * @return array
     */
    protected function snakeToCamel($data)
    {
        if (!is_array($data) && !is_object($data)) {
            return $data;
        }

        $camelData = [];
        foreach ($data as $key => $value) {
            $camelData[camel_case($key)] = $value;
        }

        return $camelData;
    }
}
