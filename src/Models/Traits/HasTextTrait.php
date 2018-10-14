<?php

namespace Tmd\LaravelHelpers\Models\Traits;

use Tmd\LaravelHelpers\Models\Base\AbstractModel;

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
