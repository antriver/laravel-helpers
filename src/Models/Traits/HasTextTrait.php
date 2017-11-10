<?php

namespace Tmd\LaravelSite\Models\Traits;

use Tmd\LaravelSite\Models\Base\AbstractModel;

trait HasTextTrait
{
    public function getText()
    {
        /** @var AbstractModel $this */
        return $this->getAttribute('text');
    }

    public function getStrippedText()
    {
        // TODO
        return $this->getText();
    }
}
