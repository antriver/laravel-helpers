<?php

namespace Tmd\LaravelSite\Libraries\Pagination;

use Illuminate\Support\HtmlString;

class LengthAwarePaginator extends \Illuminate\Pagination\LengthAwarePaginator
{
    /**
     * Get a URL for a given page number.
     * Replaces ':page' in the path with the correct page number. For pretty URLs.
     *
     * @param int $page
     *
     * @return string
     */
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        $params = [
            'page' => $page
        ];

        if ($token = \Input::get('token')) {
            $params['token'] = $token;
        }

        return $this->path.'?'.http_build_query($params);
    }

    /**
     * Render the paginator using the given view.
     * Overridden so the custom UrlWindow is used.
     *
     * @param string $view
     * @param array  $data
     *
     * @return string
     */
    public function render($view = null, $data = [])
    {
        $window = $this->getUrlWindow();

        $elements = [
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ];

        return new HtmlString(
            static::viewFactory()->make(
                $view ?: static::$defaultView,
                array_merge(
                    $data,
                    [
                        'paginator' => $this,
                        'elements' => array_filter($elements),
                    ]
                )
            )->render()
        );
    }

    /**
     * Returns the index of the first item on the current page.
     *
     * @return int
     */
    public function getFirstItemOnPage()
    {
        return ($this->currentPage() - 1) * $this->perPage() + 1;
    }

    /**
     * Returns the index of the last item on the current page.
     *
     * @return int
     */
    public function getLastItemOnPage()
    {
        return min($this->total(), $this->currentPage() * $this->perPage());
    }

    /**
     * Get the instance as an array.
     *
     * @param string $itemsKey
     *
     * @return array
     */
    public function toArray($itemsKey = 'items')
    {
        return [
            'total' => $this->total(),
            'perPage' => $this->perPage(),
            'currentPage' => $this->currentPage(),
            'lastPage' => $this->lastPage(),
            'nextPageUrl' => $this->nextPageUrl(),
            'prevPageUrl' => $this->previousPageUrl(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            $itemsKey => $this->items->toArray(),
        ];
    }

    /**
     * @return array
     */
    protected function getUrlWindow()
    {
        // Note that UrlWindow when used here is our custom one (because of the namespace)
        return UrlWindow::make($this);
    }
}
