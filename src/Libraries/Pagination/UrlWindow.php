<?php

namespace Tmd\LaravelHelpers\Libraries\Pagination;

/**
 * Class UrlWindow
 *
 * Limit the total number of pages (or dots) shown to a max of 9.
 * 1 first page + ... + 5 middle + ... + 1 last page
 *
 * @package Tmd\LaravelHelpers\Libraries\Pagination
 */
class UrlWindow extends \Illuminate\Pagination\UrlWindow
{
    /**
     * The paginator implementation.
     *
     * @var LengthAwarePaginator
     */
    protected $paginator;

    protected $maxItems = 9;

    /**
     * Get the window of URLs to be shown.
     *
     * @param  int $onEachSide
     *
     * @return array
     */
    public function get($onEachSide = null)
    {
        // ALl the pages fit in the display - just show them all.
        if ($this->paginator->lastPage() <= $this->maxItems) {
            return $this->getSmallSlider();
        }

        return $this->getUrlSlider($onEachSide);
    }

    /**
     * Create a URL slider links.
     *
     * @param  int $onEachSide
     *
     * @return array
     */
    protected function getUrlSlider($onEachSide)
    {
        if (!$this->hasPages()) {
            return [
                'first' => null,
                'slider' => null,
                'last' => null,
            ];
        }

        $endBuffer = floor($this->maxItems / 2);

        // 5 = 1 start + ... + current + ... + 1 end
        $onEachSide = floor(($this->maxItems - 5) / 2);


        if ($this->currentPage() <= $this->maxItems - $endBuffer) {
            // If the current page is very close to the beginning of the page range, we will
            // just render the beginning of the page range, followed by the last 2 of the
            // links in this list, since we will not have room to create a full slider.
            return $this->getSliderTooCloseToBeginning(null);

        } elseif ($this->currentPage() >= ($this->lastPage() - $endBuffer)) {
            // If the current page is close to the ending of the page range we will just get
            // this first couple pages, followed by a larger window of these ending pages
            // since we're too close to the end of the list to create a full on slider.
            return $this->getSliderTooCloseToEnding(null);
        }

        // If we have enough room on both sides of the current page to build a slider we
        // will surround it with both the beginning and ending caps, with this window
        // of pages in the middle providing a Google style sliding paginator setup.
        return $this->getFullSlider($onEachSide);
    }

    /**
     * Get the slider of URLs when too close to beginning of window.
     * 7 start + ... + 1 end = 9
     *
     * @param  int $window
     *
     * @return array
     */
    protected function getSliderTooCloseToBeginning($window)
    {
        return [
            'first' => $this->paginator->getUrlRange(1, $this->maxItems - 2),
            'slider' => null,
            'last' => $this->getFinish(),
        ];
    }

    /**
     * Get the slider of URLs when too close to ending of window.
     * 1 start + ... + 7 end = 9
     *
     * @param  int $window
     *
     * @return array
     */
    protected function getSliderTooCloseToEnding($window)
    {
        $last = $this->paginator->getUrlRange(
            $this->lastPage() - ($this->maxItems - 3),
            $this->lastPage()
        );

        return [
            'first' => $this->getStart(),
            'slider' => null,
            'last' => $last,
        ];
    }

    /**
     * 1 page at start.
     *
     * @return array
     */
    public function getStart()
    {
        return $this->paginator->getUrlRange(1, 1);
    }

    /**
     * 1 page at end.
     *
     * @return array
     */
    public function getFinish()
    {
        $lastPage = $this->lastPage();

        return $this->paginator->getUrlRange($lastPage, $lastPage);
    }
}
