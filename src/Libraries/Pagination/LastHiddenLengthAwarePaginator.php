<?php

namespace Tmd\LaravelSite\Libraries\Pagination;

class LastHiddenLengthAwarePaginator extends LengthAwarePaginator
{
    /**
     * @return array
     */
    protected function getUrlWindow()
    {
        // Note that UrlWindow when used here is our custom one (because of the namespace)
        return LastHiddenUrlWindow::make($this);
    }
}
