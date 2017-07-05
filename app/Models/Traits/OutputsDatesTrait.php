<?php

namespace Tmd\LaravelSite\Models\Traits;

/**
 * Provides a method to convert the dates of a model to ISO 8601
 *
 * @package Stickable
 */
trait OutputsDatesTrait
{
    protected function formatArrayDates(array $array)
    {
        foreach ($this->getDates() as $dateAttribute) {
            if (in_array($dateAttribute, $this->hidden)) {
                continue;
            }
            if (!array_key_exists($dateAttribute, $array)) {
                continue;
            }
            $value = $this->{$dateAttribute};
            if ($value) {
                $carbon = $this->asDateTime($this->{$dateAttribute});
                $array[$dateAttribute] = $carbon->format(\DateTime::ATOM);
            }
        }

        return $array;
    }
}
