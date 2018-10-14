<?php

namespace Tmd\LaravelHelpers\Models\Interfaces;

use Tmd\LaravelHelpers\Models\User;

interface BelongsToUserInterface
{
    /**
     * Return the userId that created this model.
     *
     * @return int|null
     */
    public function getUserId();

    public function getUser(): ?User;
}
