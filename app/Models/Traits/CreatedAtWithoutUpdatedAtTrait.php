<?php

namespace Tmd\LaravelSite\Models\Traits;

use Tmd\LaravelSite\Models\Base\AbstractModel;

/**
 * Class CreatedAtWithoutUpdatedAtTrait
 *
 * Allows a model to have only a createdAt column with no updatedAt
 *
 * @package Stickable
 */
trait CreatedAtWithoutUpdatedAtTrait
{
    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        $defaults = [AbstractModel::CREATED_AT];

        /** @var AbstractModel $this */

        return $this->timestamps ? array_merge($this->dates, $defaults) : $this->dates;
    }

    public function setUpdatedAt($value)
    {
        return $this;
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }
}
