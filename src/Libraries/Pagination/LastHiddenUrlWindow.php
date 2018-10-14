<?php

namespace Tmd\LaravelHelpers\Libraries\Pagination;

/**
 * Class LastHiddenUrlWindow
 *
 * Always shows 8 links
 *
 * @package Tmd\LaravelHelpers\Libraries\Pagination
 */
class LastHiddenUrlWindow extends UrlWindow
{
    /**
     * Get the ending URLs of a pagination slider.
     *
     * @return array
     */
    public function getFinish()
    {
        return [];
    }

    /**
     * Get the slider of URLs when too close to ending of window.
     * 1 start + ... + 6 end = 8
     *
     * @param  int $window
     *
     * @return array
     */
    protected function getSliderTooCloseToEnding($window)
    {
        $last = $this->paginator->getUrlRange(
            $this->lastPage() - ($this->maxItems - 4),
            $this->lastPage()
        );

        return [
            'first' => $this->getStart(),
            'slider' => null,
            'last' => $last,
        ];
    }
}
